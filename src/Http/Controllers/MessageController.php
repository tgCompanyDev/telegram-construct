<?php

namespace Valibool\TelegramConstruct\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Valibool\TelegramConstruct\Models\Message;
use Valibool\TelegramConstruct\Services\API\MessageApiService;
use Valibool\TelegramConstruct\Services\ValidationService;

class MessageController extends Controller
{
    public function __construct(protected MessageApiService  $messageApiService)
    {
    }

    /**
     * Display a listing of the messages.
     * @OA\Get(
     *               path="/api/tg-construct/message",
     *               operationId="message/index",
     *               tags={"Сообщения"},
     *               description="Все сообщения",
     *                  security = {
     *                 {"apiKey": {}},
     *                },
     *          @OA\Parameter(
     *             name="bot_id",
     *             description="bot_id",
     *             required=true,
     *             in="query",
     *              @OA\Schema(
     *                 type="integer",
     *             ),
     *         ),
     *               @OA\Response(response="200",
     *                    description="",
     *                    @OA\MediaType(
     *                        mediaType="application/json",
     *                        @OA\Schema(
     *                        ),
     *                    ),
     *                ),
     *           )
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bot_id' => 'required|exists:bots,id',
        ]);
        return $this->messageApiService->showAll($validated['bot_id']);
    }

    /**
     * Сreate message.
     * @OA\Post(
     *              path="/api/tg-construct/message",
     *              operationId="message/create",
     *              tags={"Сообщения"},
     *              description="Создание Сообщения",
     *                 security = {
     *                {"apiKey": {}},
     *               },
     *
     *              @OA\RequestBody(
     *                 @OA\MediaType(
     *                     mediaType="application/json",
     *                     @OA\Schema(
     *
     *                        @OA\Property(
     *                             property="bot_id",
     *                             type="integer"
     *                         ),
     *                      @OA\Property(
     *                             property="text",
     *                             type="string"
     *                         ),
     *                         @OA\Property(
     *                              property="type",
     *                              type="string"
     *                          ),
     *                            @OA\Property(
     *                              property="name",
     *                              type="string"
     *                          ),
     *                          @OA\Property(
     *                              property="wait_input",
     *                              type="string"
     *                          ),
     *                         @OA\Property(
     *                               property="`next_message_id`",
     *                               type="integer"
     *                           ),
     *                            @OA\Property(
     *                               property="attachment_id",
     *                               type="integer"
     *                           ),
     *
     *                       @OA\Property(
     *                                 property="buttons",
     *                                 type="json"
     *                             ),
     *
     *                         example={
     *                           "bot_id": 1,
     *                           "text": "Добро пожаловать",
     *                           "type": "message",
     *                           "wait_input": "message",
     *                           "name": "Стартовое сообщение",
     *                           "next_message_id": 1,
     *                           "attachments": {"0":1,"1":2},
     *                            "buttons": {"0":{"text":"button1","callback_data":2}},
     *                         }
     *                     )
     *                 )
     *             ),
     *              @OA\Response(response="200",
     *                   description="",
     *                   @OA\MediaType(
     *                       mediaType="application/json",
     *                       @OA\Schema(
     *                       ),
     *                   ),
     *               ),
     *          )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'type' => 'required|string|in:question,message',
            'name' => 'required|string',
            'bot_id' => 'required|exists:bots,id',
            'wait_input' => 'required_if:type,question|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'required_with:attachments|exists:tg_construct_attachments,id',
            'buttons'=>'array|nullable',
            'buttons.*.text'=>'required|string',
            'buttons.*.callback_data'=>'nullable|exists:messages,id',
        ]);
        return $this->messageApiService->store($validated);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->messageApiService->show($id);
    }

    /**
     * Update message.
     * @OA\Put(
     *             path="/api/tg-construct/message/{message_id}",
     *             operationId="message/update",
     *             tags={"Сообщения"},
     *             description="Изменения Сообщения",
     *                security = {
     *               {"apiKey": {}},
     *              },
     *     @OA\Parameter(
     *            name="message_id",
     *            description="message_id",
     *            required=true,
     *            in="path",
     *             @OA\Schema(
     *                type="integer",
     *            ),
     *        ),
     *             @OA\RequestBody(
     *                @OA\MediaType(
     *                    mediaType="application/json",
     *                    @OA\Schema(
     *
     *
     *                       @OA\Property(
     *                            property="text",
     *                            type="string"
     *                        ),
     *                        @OA\Property(
     *                             property="type",
     *                             type="string"
     *                         ),
     *                           @OA\Property(
     *                             property="name",
     *                             type="string"
     *                         ),
     *                        @OA\Property(
     *                              property="`next_message_id`",
     *                              type="integer"
     *                          ),
     *                           @OA\Property(
     *                              property="images",
     *                              type="json"
     *                          ),
     *
     *                      @OA\Property(
     *                                property="buttons",
     *                                type="json"
     *                            ),
     *
     *                        example={
     *                          "text": "Добро пожаловать",
     *                          "type": "message",
     *                          "name": "Стартовое сообщение",
     *                          "next_message_id": 1,
     *                           "attachments": {"0":1,"1":2},
     *                           "images": {"0":{"text":"button1","callback_data":2}},
     *                           "buttons": {"0":{"text":"button1","callback_data":2}},
     *                        }
     *                    )
     *                )
     *            ),
     *             @OA\Response(response="200",
     *                  description="",
     *                  @OA\MediaType(
     *                      mediaType="application/json",
     *                      @OA\Schema(
     *                      ),
     *                  ),
     *              ),
     *         )
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'type' => 'required|string',
            'name' => 'required|string',
            'next_message_id' => 'nullable|exists:messages,id',
            'attachments' => 'nullable|array',
            'attachments.*' => 'required_with:attachments|exists:tg_construct_attachments,id',
            'buttons'=>'array|nullable',
            'buttons.*.text'=>'required|string',
            'buttons.*.callback_data'=>'nullable|exists:messages,id',
        ]);
        return $this->messageApiService->update($id, $validated);
    }

    /**
     * Remove message.
     * @OA\Delete(
     *              path="/api/tg-construct/message/{message_id}",
     *              operationId="message/delete",
     *              tags={"Сообщения"},
     *              description="Удаление Сообщения",
     *                 security = {
     *                {"apiKey": {}},
     *               },
     *      @OA\Parameter(
     *             name="message_id",
     *             description="message_id",
     *             required=true,
     *             in="path",
     *              @OA\Schema(
     *                 type="integer",
     *             ),
     *         ),

     *              @OA\Response(response="200",
     *                   description="",
     *                   @OA\MediaType(
     *                       mediaType="application/json",
     *                       @OA\Schema(
     *                       ),
     *                   ),
     *               ),
     *          )
     */
    public function destroy(string $id)
    {
        return $this->messageApiService->destroy($id);

    }

    /**
     * @return JsonResponse
     * @OA\Get(
     *                path="/api/tg-construct/allowed-users-inputs",
     *                operationId="message/allowed-users-inputs",
     *                tags={"Сообщения"},
     *                description="Список ожидаемых полей для Мессаджа типа question для записи в базу через телеграмм",
     *                   security = {
     *                  {"apiKey": {}},
     *                 },
     *                @OA\Response(response="200",
     *                     description="",
     *                     @OA\MediaType(
     *                         mediaType="application/json",
     *                         @OA\Schema(
     *                         ),
     *                     ),
     *                 ),
     *            )
     */
    public function getAllowedUsersInputs()
    {
        return ValidationService::getAllowedUsersInputs();
    }

    public function constructMessagesForms(Request $request)
    {
        if(Auth::user()){
            $messages = Message::all()->sortBy('id');
            return view('messages',['messages'=>$messages]);
        }
        return view('auth');



    }
}
