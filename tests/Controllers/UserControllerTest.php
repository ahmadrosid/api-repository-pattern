<?php

use App\Services\Users\UserModel;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class UserControllerTest extends TestCase
{

    /**
     * @dataProvider registerValidationData
     */
    public function testRegisterValidation($payload, $errors)
    {
        $this->json('POST', '/users/register', $payload)
            ->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->seeJsonStructure(['status', 'errors'])
            ->seeJson(['status' => Response::HTTP_UNPROCESSABLE_ENTITY])
            ->seeJson($errors);
    }

    public function testRegister()
    {
        $payload = factory(UserModel::class)->make([
            'password' => 'secret'
        ])->getAttributes();

        $this->json('POST', '/users/register', $payload)
            ->seeStatusCode(Response::HTTP_CREATED)
            ->seeJson(Arr::only($payload, ['email', 'name']))
            ->seeInDatabase('users', Arr::only($payload, ['email', 'name']))
            ->seeJsonStructure([
                'data' => [
                    'attributes',
                    'links'
                ],
            ]);
    }

    public function testLogin()
    {
        $user = factory(UserModel::class)->create();
        $this->json('POST', '/users/login', [
                'email' => $user['email'],
                'password' => 'secret'
            ])
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJsonStructure(['access_token']);

        $access_token = json_decode(
            $this->response->getContent()
        )->access_token;

        return [
            'access_token' => $access_token,
            'user' => $user->getAttributes()
        ];
    }

    public function testInvalidLoginCredential()
    {
        $payload = [
            'email' => 'unknown@mai.com',
            'password' => 'secret'
        ];

        $this->json('POST', '/users/login', $payload)
            ->seeStatusCode(Response::HTTP_NOT_FOUND)
            ->seeJsonEquals([
                'code' => Response::HTTP_NOT_FOUND,
                'errors' => [
                    'Invalid email or username.'
                ]
            ]);
    }

    /**
     * @dataProvider loginValidationData
     */
    public function testLoginValidation($payload, $message)
    {
        $this->json('POST', '/users/login', $payload)
            ->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->seeJsonStructure(['status', 'errors'])
            ->seeJson($message);
    }

    /**
     * @depends testLogin
     */
    public function testGetUser($data)
    {
        $this->json('GET', '/users/me', [], [
            'Authorization' => "Bearer {$data['access_token']}"
        ])
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJson(Arr::only($data['user'], ['name', 'email']))
            ->seeJsonStructure([
                'data' => [
                    'type', 'id', 'attributes' => ['name', 'email'],
                    'links' => ['self']
                ]
            ]);
    }

    public function registerValidationData()
    {
        return [
            [
                'payload' => ['email' => null],
                'message' => ['The email field is required.']
            ],
            [
                'payload' => ['name' => null],
                'message' => ['The name field is required.']
            ],
            [
                'payload' => ['password' => null],
                'message' => ['The name field is required.']
            ],
            [
                'payload' => ['email' => 'x'],
                'message' => ['The email must be a valid email address.']
            ],
            [
                'payload' => ['name' => 1],
                'message' => ['The name must be a string.']
            ],
            [
                'payload' => ['password' => 'x'],
                'message' => ['The password must be at least 6 characters.']
            ],
        ];
    }

    public function loginValidationData()
    {
        return [
            [
                'payload' => ['email' => null],
                'message' => ['The email field is required.']
            ],
            [
                'payload' => ['password' => null],
                'message' => ['The password field is required.']
            ],
            [
                'payload' => ['email' => 'x'],
                'message' => ['The email must be a valid email address.']
            ],
            [
                'payload' => ['password' => 'x'],
                'message' => ['The password must be at least 6 characters.']
            ]
        ];
    }
}
