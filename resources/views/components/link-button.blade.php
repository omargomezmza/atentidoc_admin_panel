
@props([
    'text' => 'Añadir Usuario',
    'href' => route('admin.create.user'),
    'symbol' => '+',
    'image' => null
])
<style>
    .link-button {
        background-color:#4B4B4B; 
        text-align: center;
    }
    .link-button--symbol {
        font-size: 20px;
        font-weight: bold;
        color: white;
        padding-right: 14px;
    }
    .link-button:hover, .link-button:focus {
        background-color:#8d8c8c; 
    }
</style>

<a class="px-8 py-2 link-button text-gray-100 font-medium rounded-lg
    transition-colors duration-150 whitespace-nowrap"
    href="{{ $href }}">

    <span class="link-button--symbol">
        {{ $symbol }} 
    </span>
    {{ $text }}

</a>