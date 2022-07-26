<tr>
    <td data-label="№">{{ $key + 1 }}</td>
    <td data-label="Категория">{{ $item->category->name }}</td>
    <td data-label="Подкатегория">{{ $item->subcategory->name ?? null }}</td>
    <td data-label="Текст">{{ $item->text }}</td>
    <td data-label="Количество">{{ $item->count }}</td>
    <td data-label="Дата отправки">{{ $item->created_at->format('d.m.Y H:i') }}</td>
</tr>