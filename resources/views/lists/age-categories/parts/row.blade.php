<tr>
    <td data-label="№">{{ $key + 1 }}</td>
    <td data-label="Название">{{ $item->name }}</td>
    @if (request()->user()->isAdmin())
    <td class="edit" data-label="Редактирование">
        <a href="{{ route('lists.age-categories.edit', $item->id) }}">
            <i class="far fa-edit"></i>
        </a>
        <form action="{{ route('lists.age-categories.destroy', $item->id) }}" method="post">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('Вы уверены?')"><i class="fas fa-times-circle"></i></button>
        </form>
    </td>
    @endif
</tr>