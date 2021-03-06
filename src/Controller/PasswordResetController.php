<?php

namespace App\Controller;


use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Exception\ApplicationIdNotFoundException;
use App\Service\EncryptService;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Utils\RequestContextParser;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Exception\ValidatorParamNotFoundException;
use App\Service\UserService;
use App\Entity\User;
use App\Exception\AppUnauthorizedHttpException;
use Symfony\Component\Mime\Email;
use App\Utils\Email\AppEmailManager;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Validator\Exception\LogicException;
use App\Exception\AppEntityValidationException;
use Symfony\Component\Validator\Exception\ValidatorException;

class PasswordResetController
{
    private $userService;
    private $request;
    private $mailerManger;

    /**
     * RegistrationController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService, RequestStack $request, AppEmailManager $mailerManger)
    {
        $this->userService = $userService;
        $this->request = $request;
        $this->mailerManger = $mailerManger;
    }

    /**
     * @param User $data
     * @return array
     * In custom controllers By Api-Platform, The __invoke method of the action is called when the matching route is hit.
     * It can return either an instance of Symfony\Component\HttpFoundation\Response (that will be displayed to the client
     * immediately by the Symfony kernel) or, like in this example, an instance of an entity mapped as a resource
     * (or a collection of instances for collection operations). In this case, the entity will pass through all built-in event
     * listeners of API Platform. It will be automatically validated, persisted and serialized in JSON-LD. Then the Symfony kernel
     * will send the resulting document to the client.
     */
    public function __invoke(User $data): Response
    {
        $message = '';
        $API_BASE_URL = $_ENV['API_BASE_URL'];
        $SEND_EMAIL_USER = $_ENV['API_EMAIL_USER'];
        $encoder = new JsonEncode();
        $decoder = new JsonDecode();
        $encrypter = new EncryptService();
        $parser = new RequestContextParser($this->request);
        $operation = $this->request->getCurrentRequest()->attributes->get('operation');
        $now = new \DateTime('now');
        $userEmail = $data->getEmail();
        $urlExpiration = $now->add(new \DateInterval('PT8H'));

        if ($operation === 'request') {
            if (!$userEmail) {
                throw new ValidatorParamNotFoundException('email');
            }

            try {
                $this->userService->getUserByParams(array('email' => $userEmail), 'findOneBy');
            } catch (NotFoundHttpException $exception) {
                return new JsonResponse(array('message' => $exception->getMessage()));
            }

            $tokenData = array('email' => $userEmail, 'tokenExpiration' => $urlExpiration->format('Y-m-d H:i:s'));
            $tokenParam = $encrypter->encrypt($encoder->encode($tokenData, 'json'));
            $template =
                '<h3>Solicitud cambio de contraseña:</h3> 
                    <p>
                        Estimado usuario, haga click en el siguiente enlace para recuperar su contraseña --- 
                            <a href="' . $API_BASE_URL . '/#/password-recovery?tkd_reset=' . $tokenParam . '"> recuperar clave </a> 
                            (tenga en cuenta que este enlace será válido solo por 8 horas.)
                    </p>';

            $this
                ->mailerManger
                ->getCurrentEmailSender()
                ->sendEmail(
                    (new Email())
                        ->from($SEND_EMAIL_USER)
                        ->to($userEmail)
                        ->subject('Recuperación de clave de acceso')
                        ->text('Estimado cliente:')
                        ->html($template)
                );
            $message = 'Tienes un nuevo mensaje en tu correo electrónico' . $userEmail . ', por favor revise su bandeja de entrada';
        } else {
            $resetToken = str_replace(' ', '+', $parser->getRequestValue('tkd_reset'));
            $newPassword = $parser->getRequestValue('password');
            if (!$resetToken || !$newPassword) {
                $lostParam = !$resetToken ? 'tkd_reset' : 'password';
                throw new ValidatorParamNotFoundException($lostParam);
            }
            $decryptedToken = $encrypter->decrypt($resetToken);
            $dataDecoded = get_object_vars($decoder->decode($decryptedToken, 'array'));
            $expiration = new \DateTime($dataDecoded['tokenExpiration']);
            if ((new \DateTime('now')) > $expiration) {
                throw new AppUnauthorizedHttpException('', 'This address link to renew your password has been expired');
            }
            try {
                $user = $this->userService->getUserByParams(array('email' => $dataDecoded['email']), 'findOneBy');
                $user->setPassword($newPassword);
                $this->userService->updateUser($user);
            } catch (\Exception $exception) {
                if ($exception instanceof NotFoundHttpException) {
                    throw new NotFoundHttpException($exception->getMessage());
                }
                if ($exception instanceof ValidatorException) {
                    throw new AppEntityValidationException($exception->getMessage());
                }
            }

            $message = "Your password has been updated succesfully";

        }

        return new JsonResponse(array('message' => $message));
    }
}
