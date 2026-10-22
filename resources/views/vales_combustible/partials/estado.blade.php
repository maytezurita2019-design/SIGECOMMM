@php
$map=[
'EMITIDO'=>['estado-em','bi-clock','PENDIENTE'],
'COMPLETADO'=>['estado-ok','bi-check-circle','COMPLETADO'],
'ANULADO'=>['estado-no','bi-x-circle','ANULADO'],
'ENTREGADO'=>['estado-em','bi-shield-check','ENTREGADO'],
'UTILIZADO'=>['estado-ok','bi-check-circle','UTILIZADO'],
'DEVUELTO'=>['estado-po','bi-arrow-return-left','DEVUELTO'],
]; $e=$map[$vale->estado]??['estado-po','bi-circle',$vale->estado];
@endphp
<span class="estado {{ $e[0] }}"><i class="bi {{ $e[1] }}"></i>{{ $e[2] }}@if($vale->entrega_policia_id) · POLICÍA @endif</span>
