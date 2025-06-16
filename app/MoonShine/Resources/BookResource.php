<?php

declare(strict_types=1);

namespace App\MoonShine\Resources;

use Illuminate\Database\Eloquent\Model;
use App\Models\Book;

use MoonShine\Laravel\Resources\ModelResource;
use MoonShine\UI\Components\Layout\Box;
use MoonShine\UI\Fields\ID;
use MoonShine\Contracts\UI\FieldContract;
use MoonShine\Contracts\UI\ComponentContract;
use MoonShine\UI\Fields\Text;
use MoonShine\UI\Fields\Number;
use MoonShine\Support\Enums\PageType;
use MoonShine\UI\Fields\Hidden;
use Illuminate\Http\RedirectResponse;


/**
 * @extends ModelResource<Book>
 */
class BookResource extends ModelResource
{
    protected string $model = Book::class;

    protected string $title = 'Books';
    // Po zapisaniu lub edycji kieruje na strone index()
    protected PageType|null $redirectAfterSave = PageType::INDEX;
    
    /**
     * @return list<FieldContract>
     */
    // Widok listy książek
    protected function indexFields(): iterable
    {
        return [
            ID::make()->sortable(),
            Text::make('Tytuł', 'title')
                ->sortable(),
            Text::make('Autor', 'author')
                ->sortable(),
            Text::make('ISBN', 'isbn')
                ->sortable(),
            Number::make('Kopie', 'copies')
                ->sortable(),
        ];
    }

    /**
     * @return list<ComponentContract|FieldContract>
     */
    // Pole tworzenia/edycji książki
    protected function formFields(): iterable
    {
        return [
            Box::make([
                Text::make('Tytuł', 'title')->required(),
                Text::make('Autor', 'author')->required(),
                Text::make('ISBN', 'isbn'),
                Number::make('Kopie', 'copies'),
            ])
        ];
    }

    /**
     * @return list<FieldContract>
     */
    // Widok szczegółów książki
    protected function detailFields(): iterable
    {
        return [
            ID::make(),
            Text::make('Tytuł', 'title'),
            Text::make('Autor', 'author'),
            Text::make('ISBN', 'isbn'),
            Number::make('Kopie', 'copies'),
        ];
    }

    /**
     * @param Book $item
     *
     * @return array<string, string[]|string>
     * @see https://laravel.com/docs/validation#available-validation-rules
     */
    protected function rules(mixed $item): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            // ISBN może być pusty, ale jeśli jest podany, musi być unikalny i pasować do wzoru
            'isbn' => ['nullable', 'string', 'max:20', 'regex:/^[\d-]+$/', 'unique:books,isbn,' . ($item->id ?? 'null')],
            // Liczba kopii może być pusta lub musi być liczbą całkowitą
            'copies' => ['nullable', 'integer'], 
        ];
    }

    /**
     * @return iterable
     */
    protected function filters(): iterable
    {
        return [
            Text::make('Title','title'),
            Text::make('Author','author'),
                
        ];
    }
}
