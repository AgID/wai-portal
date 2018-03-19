<tr class="u-background-95">
    <td>
        <table class="footer" align="center" width="570" cellpadding="0" cellspacing="0">
            <tr>
                <td class="content-cell" align="center">
                    <div class="u-textCenter u-margin-r-all">
                        <span class="u-color-white u-textWeight-400 u-margin-r-right">{{ ucfirst(__('ui.follow_us')) }}</span>
                        <ul class="Footer-socialIcons u-inlineBlock">
                            @foreach ($site['social'] as $social)
                                <li>
                                    <a href="{{ $social['link'] }}" title="{{ $social['name'] }}">
                                        <span class="Icon-{{ $social['name'] }}"></span>
                                        <span class="u-hiddenVisually">{{ ucfirst($social['name']) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </td>
            </tr>
        </table>
    </td>
</tr>
