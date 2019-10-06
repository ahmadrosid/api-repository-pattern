<?php

use App\Services\Notes\NoteModel;
use App\Services\Users\UserModel;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Laravel\Lumen\Testing\DatabaseTransactions;

class NoteControllerTest extends TestCase
{
    use DatabaseTransactions;

    private $user;
    private $header;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(UserModel::class)->create();
        $this->header = [
            'Authorization' => "Bearer {$this->getAccessToken()}"
        ];
    }

    private function getAccessToken()
    {
        $this->json('POST', '/users/login', [
            'email' => $this->user->email,
            'password' => 'secret',
        ]);

        $access_token = json_decode(
            $this->response->getContent()
        )->access_token;

        return $access_token;
    }

    public function testBrowseNotes()
    {
        $pageLimit = 15;
        $totalNotes = 20;

        factory(NoteModel::class, $totalNotes)->create([
            'user_id' => $this->user->id
        ]);

        $this->json('GET', '/notes', [], $this->header)
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJsonStructure([
                'data' => [
                    ['type', 'id', 'links', 'attributes' => ['title', 'text', 'user_id', 'created_at', 'updated_at']]
                ],
                'meta' => ['total', 'count'],
                'links' => ['self', 'first', 'next', 'last']
            ])
            ->seeJson([
                'total' => $totalNotes,
                'count' => $pageLimit
            ]);
    }

    public function testShowNote()
    {
        $note = factory(NoteModel::class)->create([
            'user_id' => $this->user->id
        ]);

        $note_id = Arr::pull($note, 'id');

        $payload = array_merge($note->toArray(),
            [
                'created_at' => $note->created_at->toISOString(),
                'updated_at' => $note->created_at->toISOString()
            ]
        );

        $this->json('GET', "/notes/$note_id", [], $this->header)
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJsonStructure([
                'data' => [
                    'type', 'id',
                    'attributes' => ['title', 'text', 'user_id', 'created_at', 'updated_at'],
                    'links' => ['self']
                ]
            ])
            ->seeJson($payload);
    }

    public function testShowNotFound()
    {
        $note_id = 0;

        $this->json('GET', "/notes/$note_id", [], $this->header)
            ->seeStatusCode(Response::HTTP_NOT_FOUND)
            ->seeJson([
                'status' => Response::HTTP_NOT_FOUND,
                'errors' => [
                    "No query results for model [App\\Services\\Notes\\NoteModel] $note_id"
                ]
            ]);
    }

    public function testStoreNote()
    {
        $payload = factory(NoteModel::class)->make()->toArray();

        $this->json('POST', '/notes', $payload, $this->header)
            ->seeStatusCode(Response::HTTP_CREATED)
            ->seeJson($payload)
            ->seeJsonStructure([
                'data' => [
                    'type', 'id', 'links',
                    'attributes' => ['title', 'text', 'user_id', 'created_at', 'updated_at'],
                    'links' => ['self']
                ],
            ]);
    }

    /**
     * @dataProvider validationNoteDate
     */
    public function testStoreNoteValidation($payload, $message)
    {
        $this->json('POST', '/notes', $payload, $this->header)
            ->seeStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->seeJsonStructure(['status', 'errors'])
            ->seeJson(['status' => Response::HTTP_UNPROCESSABLE_ENTITY])
            ->seeJson($message);
    }

    public function validationNoteDate()
    {
        return [
            [
                'payload' => ['title' => null],
                'message' => ['The title field is required.']
            ],
            [
                'payload' => ['text' => null],
                'message' => ['The text field is required.']
            ],
            [
                'payload' => ['title' => 1],
                'message' => ['The title must be a string.']
            ],
            [
                'payload' => ['text' => 1],
                'message' => ['The text must be a string.']
            ]
        ];
    }
}
