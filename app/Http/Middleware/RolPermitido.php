<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class RolPermitido
{
 public function handle(Request $request, Closure $next, string ...$roles): Response
 {
   $usuario=$request->user(); abort_unless($usuario,401);
   $actual=mb_strtoupper(trim((string)optional($usuario->rol)->nombre));
   $permitidos=array_map(fn($r)=>mb_strtoupper(trim($r)),$roles);
   abort_unless(in_array($actual,$permitidos,true),403,'No tiene permiso para realizar esta operación.');
   return $next($request);
 }
}
