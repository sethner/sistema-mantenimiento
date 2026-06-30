@props(['url', 'message' => null])
<tr>
<td class="header" style="text-align: center; padding: 25px 0;">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
<span style="font-family: 'Outfit', 'Inter', Helvetica, Arial, sans-serif; font-size: 20px; font-weight: 800; color: #4f46e5; letter-spacing: 0.5px;">
    {{ $config->nombre_institucion ?? config('app.name', 'Soporte Técnico') }}
</span>
</a>
</td>
</tr>

