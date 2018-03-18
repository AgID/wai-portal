@if (isset($internal))
<ul>
@else
@php $items = $site['menu_items'] @endphp
<ul class="Linklist Linklist--padded u-layout-prose u-text-r-xs Treeview Treeview--default js-Treeview">
@endif
  @foreach ($items as $item)
  @if ($item['url'] != '#' && (strpos($item['url'], '//') === false))
  @php $item['url'] = url($item['url']) @endphp
  @endif
	<li data-megamenu-class="Megamenu-item @if ($item['url'] == url(request()->path())) is-current @endif">
		<a href="{{ $item['url'] }}" class="@if ($item['url'] == url(request()->path())) is-current u-textWeight-700 @endif Linklist-link"
      data-megamenu-class="@if ($item['url'] == url(request()->path())) is-current @endif">
			{{ __('ui.pages.'.$item['name'].'.title') }}
		</a>
    @isset ($item['subitems'])
      @include('layouts.includes.linklist', ['internal' => true, 'items' => $item['subitems']])
  	@endisset
	</li>
  @endforeach
</ul>
