<?php

namespace Valibool\TelegramConstruct\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Valibool\TelegramConstruct\Services\API\MessageApiService;

class MessageController extends Controller
{
    public function __construct(protected MessageApiService  $messageApiService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->messageApiService->showAll();
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
     *                           "name": "Стартовое сообщение",
     *                           "next_message_id": 1,
     *                           "attachment_id": 1,
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
            'type' => 'required|string',
            'name' => 'required|string',
            'bot_id' => 'required|exists:bots,id',
            'attachment_id' => 'nullable|exists:tg_construct_attachments,id',
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
     *            name="messaage_id",
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
     *                              property="attachment_id",
     *                              type="integer"
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
     *                          "attachment_id": 1,
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
            'attachment_id' => 'nullable|exists:tg_construct_attachments,id',
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
     * @param int $message_id
     * @param Request $request
     * @return JsonResponse
     */
    public function syncButtons(int $message_id, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'buttons'=>'array|nullable',
            'buttons.*.text'=>'required|string',
            'buttons.*.callback_data'=>'required|exists:messages,id',
        ]);

        return $this->messageApiService->syncButtons($message_id, $validated);
    }
    public function confirmSaveAll(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bot_id' => 'required|exists:bots,id',
        ]);

        return $this->messageApiService->confirmSaveAll($validated['bot_id']);
    }
}
