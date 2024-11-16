<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Validator;

class ItemController extends Controller
{
    public function index(Request $request)
    {

        $item = Item::all();

        return response()->json(['Предметы' => $item], 200);
    }

    // Метод для добавления нового предмета
    public function store(Request $request)
    {
        // Проверка, авторизован ли пользователь
        if (!auth('api')->check()) {
            return response()->json(['error' => 'Пройдите авторизацию'], 401);
        }

        // Проверка, является ли пользователь администратором
        if (auth('api')->user()->role_id !== 1) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Валидация данных запроса
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'description' => 'required|string|min:6|max:255',
            'cost' => 'required|integer|min:3|max:9999',
            'required_item' => 'nullable|integer|exists:items,id',
            'tier_id' => 'required|integer|exists:tier,id',
            'type_id' => 'required|integer|exists:type,id',
            'image' => 'nullable|string|max:255',
        ]);

        // Создание нового предмета
        $item = Item::create($validated);

        // Возвращаем успешный ответ
        return response()->json($item, 201);
    }

    // Метод для редактирования предмета
    public function update(Request $request, $id)
    {
        // Проверка, авторизован ли пользователь
        if (!auth('api')->check()) {
            return response()->json(['error' => 'Пройдите авторизацию'], 401);
        }

        // Проверка, является ли пользователь администратором
        if (auth('api')->user()->role_id !== 1) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Находим предмет по id
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['error' => 'Предмет не найден'], 404);
        }

        // Определяем правила валидации
        $rules = [
            'name' => 'string|min:3|max:255',
            'description' => 'string|min:6|max:255',
            'cost' => 'integer|min:3|max:9999',
            'required_item' => 'nullable|integer|exists:items,id',
            'tier_id' => 'integer|exists:tiers,id',
            'type_id' => 'integer|exists:types,id',
            'image' => 'nullable|string|max:255',
        ];

        // Валидация данных
        $validated = $request->validate($rules);

        // Проверяем, что хотя бы одно поле передано
        if (empty($validated)) {
            return response()->json(['error' => 'Нужно указать хотя бы одно поле для обновления'], 422);
        }

        // Обновляем только переданные поля
        $item->update($validated);

        // Возвращаем обновлённый предмет
        return response()->json($item, 200);
    }

    // Метод для удаления предмета
    public function destroy($id)
    {
        // Проверка, авторизован ли пользователь
        if (!auth('api')->check()) {
            return response()->json(['error' => 'Пройдите авторизацию'], 401);
        }

        // Проверка, является ли пользователь администратором
        if (auth('api')->user()->role_id !== 1) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Находим предмет по id
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['error' => 'Предмет не найден'], 404);
        }

        // Удаляем предмет
        $item->delete();

        // Возвращаем успешный ответ
        return response()->json(null, 204);
    }
}
