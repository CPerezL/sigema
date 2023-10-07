<?php

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas de oriente nuevo
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
//registro e tramite
Route::prefix('oruno_tramite_ini_1')->as('oruno_tramite_ini_1')->controller(App\Http\Controllers\Oruno\Oruno_tramite_ini_1::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::get('get_tramite', 'get_tramite');
    Route::get('get_nlogia', 'get_nlogia');
    Route::post('save_tramite', 'save_tramite')->middleware('escritura');
    Route::post('update_tramite', 'update_tramite')->middleware('escritura');
});
//aprobacion de datos de tramite
Route::prefix('oruno_tramite_ini_2')->as('oruno_tramite_ini_2')->controller(App\Http\Controllers\Oruno\Oruno_tramite_ini_2::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::get('get_tramite', 'get_tramite');
    Route::post('update_tramite', 'update_tramite')->middleware('escritura');
});
//generar circulares
Route::prefix('oruno_tramite_ini_3')->as('oruno_tramite_ini_3')->controller(App\Http\Controllers\Oruno\Oruno_tramite_ini_3::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::post('get_circular', 'get_circular');
});
//circulares generados para imprimir

Route::prefix('oruno_tramite_ini_circulares')->as('oruno_tramite_ini_circulares')->controller(App\Http\Controllers\Oruno\Oruno_tramite_ini_circulares::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::post('ver_circular', 'gen_circular');
});
//lista de tramites con circular para observar
Route::prefix('oruno_tramite_ini_4')->as('oruno_tramite_ini_4')->controller(App\Http\Controllers\Oruno\Oruno_tramite_ini_4::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::get('get_tramite', 'get_tramite');
    Route::post('update_tramite', 'update_tramite')->middleware('escritura');
    Route::post('save_observacion', 'save_observacion')->middleware('escritura');

});
///pago de tramites
Route::prefix('oruno_tramite_ini_5')->as('oruno_tramite_ini_5')->controller(App\Http\Controllers\Oruno\Oruno_tramite_ini_5::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::get('get_ceremonia', 'get_ceremonia');
    // Route::post('get_datos_pagos', 'get_datos_pagos');
    Route::any('update_tramite', 'update_tramite');
});
// generar rum para tramites pagados
Route::prefix('oruno_tramite_ini_6')->as('oruno_tramite_ini_6')->controller(App\Http\Controllers\Oruno\Oruno_tramite_ini_6::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::any('procesar_datos', 'procesar_datos');
    Route::post('get_nombres', 'get_nombres');
    Route::any('gen_reporte', 'gen_reporte');
});
//registro e tramite
Route::prefix('oruno_tramite_aum_1')->as('oruno_tramite_aum_1')->controller(App\Http\Controllers\Oruno\Oruno_tramite_aum_1::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::get('get_tramite', 'get_tramite');
    Route::post('save_tramite', 'save_tramite')->middleware('escritura');
});
//aprobacion de datos de tramite
Route::prefix('oruno_tramite_aum_2')->as('oruno_tramite_aum_2')->controller(App\Http\Controllers\Oruno\Oruno_tramite_aum_2::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::get('get_tramite', 'get_tramite');
    Route::post('update_tramite', 'update_tramite')->middleware('escritura');
});
Route::prefix('oruno_tramite_aum_3')->as('oruno_tramite_aum_3')->controller(App\Http\Controllers\Oruno\Oruno_tramite_aum_3::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::get('get_ceremonia', 'get_ceremonia');
    Route::post('update_tramite', 'update_tramite')->middleware('escritura');
});
Route::prefix('oruno_tramite_aum_4')->as('oruno_tramite_aum_4')->controller(App\Http\Controllers\Oruno\Oruno_tramite_aum_4::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::post('get_logias', 'get_logias');
    Route::post('update_data', 'update_data')->middleware('escritura');
    Route::get('gen_reporte', 'gen_reporte');
});

Route::prefix('oruno_tramite_exa_1')->as('oruno_tramite_exa_1')->controller(App\Http\Controllers\Oruno\Oruno_tramite_exa_1::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::get('get_tramite', 'get_tramite');
    Route::post('save_tramite', 'save_tramite')->middleware('escritura');
});
Route::prefix('oruno_tramite_exa_2')->as('oruno_tramite_exa_2')->controller(App\Http\Controllers\Oruno\Oruno_tramite_exa_2::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::get('get_tramite', 'get_tramite');
    Route::post('update_tramite', 'update_tramite')->middleware('escritura');
});
Route::prefix('oruno_tramite_exa_3')->as('oruno_tramite_exa_3')->controller(App\Http\Controllers\Oruno\Oruno_tramite_exa_3::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::get('get_ceremonia', 'get_ceremonia');
    Route::post('update_tramite', 'update_tramite')->middleware('escritura');
});
Route::prefix('oruno_tramite_exa_4')->as('oruno_tramite_exa_4')->controller(App\Http\Controllers\Oruno\Oruno_tramite_exa_4::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::post('get_logias', 'get_logias');
    Route::post('update_data', 'update_data')->middleware('escritura');
    Route::get('gen_reporte', 'gen_reporte');
});
///afiliacin
Route::prefix('oruno_tramite_afilia_1')->as('oruno_tramite_afilia_1')->controller(App\Http\Controllers\Oruno\Oruno_tramite_afilia_1::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('get_miembros', 'get_miembros');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::post('save_tramite', 'save_tramite')->middleware('escritura');
    Route::post('unset_tramite', 'unset_tramite')->middleware('escritura');
    Route::post('registra_datos', 'registra_datos')->middleware('escritura');
    // Route::post('cambia_datos', 'cambia_datos')->middleware('escritura');
});
///afiliacin
Route::prefix('oruno_tramite_afilia_2')->as('oruno_tramite_afilia_2')->controller(App\Http\Controllers\Oruno\Oruno_tramite_afilia_2::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::any('cambia_datos', 'cambia_datos')->middleware('escritura');
});
Route::prefix('oruno_tramite_afilia_3')->as('oruno_tramite_afilia_3')->controller(App\Http\Controllers\Oruno\Oruno_tramite_afilia_3::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtro
    //Route::post('get_pago_datos', 'get_pago_datos');
    Route::post('registra_pago', 'registra_pago')->middleware('escritura');
    //Route::get('get_values', 'get_values');
});
Route::prefix('oruno_tramite_afilia_4')->as('oruno_tramite_afilia_4')->controller(App\Http\Controllers\Oruno\Oruno_tramite_afilia_4::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::post('cambia_datos', 'cambia_datos')->middleware('escritura');
    Route::post('cambia_logia', 'cambia_logia')->middleware('escritura');
    Route::get('gen_reporte', 'gen_reporte');
});
Route::prefix('oruno_tramite_afilia_5')->as('oruno_tramite_afilia_5')->controller(App\Http\Controllers\Oruno\Oruno_tramite_afilia_5::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    // Route::post('cambia_datos', 'cambia_datos')->middleware('escritura');
    // Route::post('cambia_logia', 'cambia_logia')->middleware('escritura');
});
Route::prefix('oruno_tramite_ini_certificados')->as('oruno_tramite_ini_certificados')->controller(App\Http\Controllers\Oruno\Oruno_tramite_ini_certificados::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::post('update_ceremonia', 'update_ceremonia')->middleware('escritura'); //actualiza
    Route::post('get_logias', 'get_logias');
    Route::get('get_tramites', 'get_tramites');
});

Route::prefix('oruno_tramite_aum_certificados')->as('oruno_tramite_aum_certificados')->controller(App\Http\Controllers\Oruno\Oruno_tramite_aum_certificados::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::any('update_ceremonia', 'update_ceremonia')->middleware('escritura'); //actualiza
    Route::post('get_logias', 'get_logias');
    Route::get('get_tramites', 'get_tramites');
});
Route::prefix('oruno_tramite_exa_certificados')->as('oruno_tramite_exa_certificados')->controller(App\Http\Controllers\Oruno\Oruno_tramite_exa_certificados::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::any('update_ceremonia', 'update_ceremonia')->middleware('escritura'); //actualiza
    Route::post('get_logias', 'get_logias');
    Route::get('get_tramites', 'get_tramites');
});
//registro e tramite
Route::prefix('oruno_tramite_regu_1')->as('oruno_tramite_regu_1')->controller(App\Http\Controllers\Oruno\Oruno_tramite_regu_1::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::get('get_tramite', 'get_tramite');
    Route::get('get_nlogia', 'get_nlogia');
    Route::post('save_tramite', 'save_tramite')->middleware('escritura');
    Route::post('update_tramite', 'update_tramite')->middleware('escritura');
});
//aprobacion de datos de tramite
Route::prefix('oruno_tramite_regu_2')->as('oruno_tramite_regu_2')->controller(App\Http\Controllers\Oruno\Oruno_tramite_regu_2::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::get('get_tramite', 'get_tramite');
    Route::post('update_tramite', 'update_tramite')->middleware('escritura');
});
//generar circulares
Route::prefix('oruno_tramite_regu_3')->as('oruno_tramite_regu_3')->controller(App\Http\Controllers\Oruno\Oruno_tramite_regu_3::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::post('get_circular', 'get_circular');
});
//circulares generados para imprimir
Route::prefix('oruno_tramite_regu_circulares')->as('oruno_tramite_regu_circulares')->controller(App\Http\Controllers\Oruno\Oruno_tramite_regu_circulares::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::post('ver_circular', 'gen_circular');
});
//lista de tramites con circular para observar
Route::prefix('oruno_tramite_regu_4')->as('oruno_tramite_regu_4')->controller(App\Http\Controllers\Oruno\Oruno_tramite_regu_4::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::get('get_tramite', 'get_tramite');
    Route::post('update_tramite', 'update_tramite')->middleware('escritura');
    Route::post('save_observacion', 'save_observacion')->middleware('escritura');

});
///pago de tramites
Route::prefix('oruno_tramite_regu_5')->as('oruno_tramite_regu_5')->controller(App\Http\Controllers\Oruno\Oruno_tramite_regu_5::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::get('get_ceremonia', 'get_ceremonia');
    Route::any('update_tramite', 'update_tramite');
});
// generar rum para tramites pagados
Route::prefix('oruno_tramite_regu_6')->as('oruno_tramite_regu_6')->controller(App\Http\Controllers\Oruno\Oruno_tramite_regu_6::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::any('procesar_datos', 'procesar_datos');
    Route::post('get_nombres', 'get_nombres');
    Route::any('gen_reporte', 'gen_reporte');
    Route::get('get_tramites', 'get_tramites');
    Route::any('update_ceremonia', 'update_ceremonia')->middleware('escritura'); //actualiza
});

Route::prefix('triangulos')->as('triangulos')->controller(App\Http\Controllers\Oruno\Triangulos::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::post('save_datos', 'save_datos')->middleware('escritura'); //inserta
    Route::post('update_datos', 'update_datos')->middleware('escritura'); //actualiza
    Route::post('destroy_datos', 'destroy_datos')->middleware('escritura'); //borras
    Route::post('show_papelera', 'show_papelera')->middleware('escritura'); //borras
    Route::get('get_orientes', 'get_orientes');
    Route::get('get_valles', 'get_valles');
    Route::post('convertir', 'convertir')->middleware('escritura'); //borras
});
Route::prefix('tramite_retiro_1')->as('tramite_retiro_1')->controller(App\Http\Controllers\Oruno\Tramite_retiro_1::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('get_miembros', 'get_miembros');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::post('save_tramite', 'save_tramite')->middleware('escritura');
    Route::post('unset_tramite', 'unset_tramite')->middleware('escritura');
    //Route::post('registra_datos', 'registra_datos')->middleware('escritura');
    Route::post('update_tramite', 'update_tramite');
});
Route::prefix('tramite_retiro_2')->as('tramite_retiro_2')->controller(App\Http\Controllers\Oruno\Tramite_retiro_2::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::any('cambia_datos', 'cambia_datos')->middleware('escritura');
    Route::get('gen_certificado', 'gen_certificado');
});

Route::prefix('tramite_rehabilitacion_1')->as('tramite_rehabilitacion_1')->controller(App\Http\Controllers\Oruno\Tramite_rehabilitacion_1::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::post('get_miembros', 'get_miembros');
    Route::post('save_tramite', 'save_tramite')->middleware('escritura'); //inserta
    Route::post('cambia_tramite', 'cambia_tramite')->middleware('escritura');
    Route::post('unset_tramite', 'unset_tramite')->middleware('escritura'); //inserta
    Route::post('cambia_datos', 'cambia_datos')->middleware('escritura'); //aun falta
    Route::post('registra_pago', 'registra_pago')->middleware('escritura');
    Route::get('gen_certificado', 'gen_certificado');
});
Route::prefix('tramite_rehabilitacion_2')->as('tramite_rehabilitacion_2')->controller(App\Http\Controllers\Oruno\Tramite_rehabilitacion_2::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::post('get_miembros', 'get_miembros');
    Route::post('save_tramite', 'save_tramite')->middleware('escritura'); //inserta
    Route::post('cambia_tramite', 'cambia_tramite')->middleware('escritura');
    Route::post('unset_tramite', 'unset_tramite')->middleware('escritura'); //inserta
    Route::post('cambia_datos', 'cambia_datos')->middleware('escritura'); //aun falta
    Route::post('registra_pago', 'registra_pago')->middleware('escritura');
    Route::get('gen_certificado', 'gen_certificado');
});
Route::prefix('tramite_rehabilitacion_3')->as('tramite_rehabilitacion_3')->controller(App\Http\Controllers\Oruno\Tramite_rehabilitacion_3::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::post('get_miembros', 'get_miembros');
    Route::post('save_tramite', 'save_tramite')->middleware('escritura'); //inserta
    Route::post('cambia_tramite', 'cambia_tramite')->middleware('escritura');
    Route::post('unset_tramite', 'unset_tramite')->middleware('escritura'); //inserta
    Route::post('cambia_datos', 'cambia_datos')->middleware('escritura'); //aun falta
    Route::any('registra_pago', 'registra_pago')->middleware('escritura');
    Route::get('gen_certificado', 'gen_certificado');
});
Route::prefix('tramite_rehabilitacion_4')->as('tramite_rehabilitacion_4')->controller(App\Http\Controllers\Oruno\Tramite_rehabilitacion_4::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::post('get_miembros', 'get_miembros');
    Route::post('save_tramite', 'save_tramite')->middleware('escritura'); //inserta
    Route::post('cambia_tramite', 'cambia_tramite')->middleware('escritura');
    Route::post('unset_tramite', 'unset_tramite')->middleware('escritura'); //inserta
    Route::post('cambia_datos', 'cambia_datos')->middleware('escritura'); //aun falta
    Route::post('registra_pago', 'registra_pago')->middleware('escritura');
    Route::get('gen_certificado', 'gen_certificado');
});
//nueov modulo de membrecia en N logias
Route::prefix('logias_membrecia')->as('logias_membrecia')->controller(App\Http\Controllers\Oruno\Logias_membrecia::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::any('save_datos', 'save_datos')->middleware('escritura'); //inserta
    Route::any('get_logias', 'get_logias');
    Route::post('destroy_datos', 'destroy_datos')->middleware('escritura'); //borras
});
Route::prefix('ritos_admin')->as('ritos_admin')->controller(App\Http\Controllers\Ritos_admin::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::any('get_oficiales', 'get_oficiales');
    Route::any('update_oficial', 'update_oficial')->middleware('escritura');
    Route::any('save_oficial', 'save_oficial')->middleware('escritura');
    Route::post('destroy_oficial', 'destroy_oficial')->middleware('escritura'); //borras
    Route::any('update_rito', 'update_rito')->middleware('escritura');
    Route::any('save_rito', 'save_rito')->middleware('escritura');
    Route::post('destroy_rito', 'destroy_rito')->middleware('escritura'); //borras
});
Route::prefix('formularios_obolos')->as('formularios_obolos')->controller(App\Http\Controllers\Oruno\Formularios_obolos::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('get_miembros', 'get_miembros');
    Route::post('get_formularios', 'get_formularios');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::any('send_formulario', 'send_formulario');
    Route::post('anular_formulario', 'anular_formulario');

    Route::post('createqr_formaporte', 'createqr_formaporte')->middleware('escritura');
    Route::post('set_pagomiembro', 'set_pagomiembro')->middleware('escritura');
    Route::post('remove_obolo', 'remove_obolo')->middleware('escritura');
    Route::get('gen_qrformulario', 'gen_qrformulario');

    Route::get('get_datos_qr', 'get_datos_qr');
    Route::post('get_pago_qr', 'get_pago_qr');
    Route::get('gen_recibo', 'gen_recibo');
});
///pago de tramites deretiro
Route::prefix('tramite_retiro_3')->as('tramite_retiro_3')->controller(App\Http\Controllers\Oruno\Tramite_retiro_3::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtro
    Route::post('registra_pago', 'registra_pago')->middleware('escritura');
});
///pago de tramites deretiro
Route::prefix('tramite_retiro_4')->as('tramite_retiro_4')->controller(App\Http\Controllers\Oruno\Tramite_retiro_4::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::post('cambia_datos', 'cambia_datos')->middleware('escritura');
    Route::get('gen_certificado', 'gen_certificado');
    Route::post('update_data', 'update_data')->middleware('escritura');
});
Route::prefix('tramite_retiro_5')->as('tramite_retiro_5')->controller(App\Http\Controllers\Oruno\Tramite_retiro_5::class)->middleware(['auth', 'permission'])->group(function () {
    Route::any('/', 'index');
    Route::post('get_datos', 'get_datos');
    Route::post('filtrar', 'filtrar'); //filtros de datagrid
    Route::post('cambia_datos', 'cambia_datos')->middleware('escritura');
    Route::post('extender', 'extender')->middleware('escritura');
    Route::get('gen_certificado', 'gen_certificado');
});
