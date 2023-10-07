<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', [App\Http\Controllers\Inicio::class, 'index'])->name('home')->middleware('sites');
Route::get('login', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login')->middleware('sites');
Route::post('login', 'App\Http\Controllers\Auth\LoginController@login');
Route::any('logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');
Route::any('salir', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');
Route::post('change_user', 'App\Http\Controllers\Auth\LoginController@change_user')->middleware('auth'); //revisar

Route::any('/sistema', [App\Http\Controllers\Inicio::class, 'index'])->name('sistema')->middleware('sites');
Route::group(
    ['prefix' => 'inicio', 'as' => 'inicio', 'middleware' => ['sites']], function () {
        Route::any('/', [App\Http\Controllers\Inicio::class, 'index']);
        Route::post('update_template', [App\Http\Controllers\Inicio::class, 'update_template']);
        Route::post('update_clave', [App\Http\Controllers\Inicio::class, 'update_clave']);
        Route::post('get_datos', [App\Http\Controllers\Inicio::class, 'get_datos']);
    }
);
//01
Route::group(
    ['prefix' => 'usuarios', 'as' => 'usuarios', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Usuarios::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Usuarios::class, 'get_datos']);
        Route::get('get_orientes', [App\Http\Controllers\Usuarios::class, 'get_orientes']);
        Route::get('get_logias', [App\Http\Controllers\Usuarios::class, 'get_logias']);
        Route::get('get_valles', [App\Http\Controllers\Usuarios::class, 'get_valles']);
        Route::post('filtrar', [App\Http\Controllers\Usuarios::class, 'filtrar']); //filtros de datagrid
        Route::post('save_datos', [App\Http\Controllers\Usuarios::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('update_datos', [App\Http\Controllers\Usuarios::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('destroy_datos', [App\Http\Controllers\Usuarios::class, 'destroy_datos'])->middleware('escritura'); //borras
    }
);
//02
Route::group(
    ['prefix' => 'roles', 'as' => 'roles', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Roles::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Roles::class, 'get_datos']);
        Route::post('save_datos', [App\Http\Controllers\Roles::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('update_datos', [App\Http\Controllers\Roles::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('destroy_datos', [App\Http\Controllers\Roles::class, 'destroy_datos'])->middleware('escritura'); //borras
        Route::post('filtrar', [App\Http\Controllers\Roles::class, 'filtrar']); //filtros de datagrid
    }
);
//03
Route::group(
    ['prefix' => 'modulos', 'as' => 'modulos', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Modulos::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Modulos::class, 'get_datos']);
        Route::post('save_datos', [App\Http\Controllers\Modulos::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('update_datos', [App\Http\Controllers\Modulos::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('destroy_datos', [App\Http\Controllers\Modulos::class, 'destroy_datos'])->middleware('escritura'); //borras
        Route::post('filtrar', [App\Http\Controllers\Modulos::class, 'filtrar']); //filtros de datagrid
        Route::get('get_roles', [App\Http\Controllers\Modulos::class, 'get_roles']); //----------------------------------
        Route::post('update_roles', [App\Http\Controllers\Modulos::class, 'update_roles']); //----------------------------------
    }
);
//04
Route::group(
    ['prefix' => 'menus', 'as' => 'menus', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Menus::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Menus::class, 'get_datos']);
        Route::post('save_datos', [App\Http\Controllers\Menus::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('update_datos', [App\Http\Controllers\Menus::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('destroy_datos', [App\Http\Controllers\Menus::class, 'destroy_datos'])->middleware('escritura'); //borras
        Route::post('filtrar', [App\Http\Controllers\Menus::class, 'filtrar']); //filtros de datagrid
        Route::get('get_roles', [App\Http\Controllers\Menus::class, 'get_roles']); //----------------------------------
        Route::post('update_roles', [App\Http\Controllers\Menus::class, 'update_roles'])->middleware('escritura');; //----------------------------------
        Route::post('fixmenu_datos', [App\Http\Controllers\Menus::class, 'fixmenu_datos'])->middleware('escritura');;
        Route::post('fixmenu_padres', [App\Http\Controllers\Menus::class, 'fixmenu_padres'])->middleware('escritura');;
        Route::post('menu_estado', [App\Http\Controllers\Menus::class, 'menu_estado'])->middleware('escritura');;
        Route::post('menu_revisado', [App\Http\Controllers\Menus::class, 'menu_revisado'])->middleware('escritura');;
    }
);
//06
Route::group(
    ['prefix' => 'orientes', 'as' => 'orientes', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Orientes::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Orientes::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Orientes::class, 'filtrar']); //filtros de datagrid
        Route::post('save_datos', [App\Http\Controllers\Orientes::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('update_datos', [App\Http\Controllers\Orientes::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('destroy_datos', [App\Http\Controllers\Orientes::class, 'destroy_datos'])->middleware('escritura'); //borras
    }
);
//07
Route::group(
    ['prefix' => 'valles', 'as' => 'valles', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Valles::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Valles::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Valles::class, 'filtrar']); //filtros de datagrid
        Route::post('save_datos', [App\Http\Controllers\Valles::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('update_datos', [App\Http\Controllers\Valles::class, 'update_datos'])->middleware('escritura'); //actualiza
        // Route::post('destroy_datos', [App\Http\Controllers\Valles::class, 'destroy_datos'])->middleware('escritura'); //borras
        // Route::post('show_papelera', [App\Http\Controllers\Valles::class, 'show_papelera'])->middleware('escritura'); //borras
        Route::post('destroy_valle', [App\Http\Controllers\Valles::class, 'destroy_valle'])->middleware('escritura'); //borras
    }
);
//08
Route::group(
    ['prefix' => 'logias', 'as' => 'logias', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Logias::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Logias::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Logias::class, 'filtrar']); //filtros de datagrid
        Route::post('save_datos', [App\Http\Controllers\Logias::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('update_datos', [App\Http\Controllers\Logias::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('destroy_datos', [App\Http\Controllers\Logias::class, 'destroy_datos'])->middleware('escritura'); //borras
        Route::post('show_papelera', [App\Http\Controllers\Logias::class, 'show_papelera'])->middleware('escritura'); //borras
        Route::get('get_orientes', [App\Http\Controllers\Logias::class, 'get_orientes']);
        Route::get('get_valles', [App\Http\Controllers\Logias::class, 'get_valles']);
        Route::post('convertir', [App\Http\Controllers\Logias::class, 'convertir'])->middleware('escritura'); //borras
    }
);
//09
Route::group(
    ['prefix' => 'importar_miembros', 'as' => 'importar_miembros', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Importar_miembros::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Importar_miembros::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Importar_miembros::class, 'filtrar']); //filtros de datagrid
        Route::post('upload_file', [App\Http\Controllers\Importar_miembros::class, 'upload_test'])->middleware('escritura'); //inserta
    }
);
//10
Route::group(
    ['prefix' => 'membrecia', 'as' => 'membrecia', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Membrecia::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Membrecia::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Membrecia::class, 'filtrar']); //filtros de datagrid
        Route::get('get_form', [App\Http\Controllers\Membrecia::class, 'get_form']);
        Route::post('save_datos', [App\Http\Controllers\Membrecia::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('update_datos', [App\Http\Controllers\Membrecia::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('destroy_datos', [App\Http\Controllers\Membrecia::class, 'destroy_datos'])->middleware('escritura'); //borras
    }
);
//05
Route::group(
    ['prefix' => 'configuracion', 'as' => 'configuracion', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Configuracion::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Configuracion::class, 'get_datos']);
        Route::post('update_datos', [App\Http\Controllers\Configuracion::class, 'update_datos'])->middleware('escritura'); //actualiza
    }
);
//11
Route::group(
    ['prefix' => 'membrecia_clave', 'as' => 'membrecia_clave', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Membrecia_clave::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Membrecia_clave::class, 'get_datos']);
        Route::post('update_datos', [App\Http\Controllers\Membrecia_clave::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('save_datos', [App\Http\Controllers\Membrecia_clave::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('destroy_datos', [App\Http\Controllers\Membrecia_clave::class, 'destroy_datos'])->middleware('escritura'); //borras
    }
);
//12
Route::group(
    ['prefix' => 'valles_datos', 'as' => 'valles_datos', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Valles_datos::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Valles_datos::class, 'get_datos']);
        Route::post('update_datos', [App\Http\Controllers\Valles_datos::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('save_datos', [App\Http\Controllers\Valles_datos::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('destroy_datos', [App\Http\Controllers\Valles_datos::class, 'destroy_datos'])->middleware('escritura'); //borras
        Route::get('get_form', [App\Http\Controllers\Valles_datos::class, 'get_form']);
    }
);
//13
Route::group(
    ['prefix' => 'comap_listas', 'as' => 'comap_listas', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Comap_listas::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Comap_listas::class, 'get_datos']);
        Route::get('get_valles', [App\Http\Controllers\Comap_listas::class, 'get_valles']);
        Route::get('get_logias', [App\Http\Controllers\Comap_listas::class, 'get_logias']);
        Route::post('set_datos', [App\Http\Controllers\Comap_listas::class, 'set_datos']);
    }
);
//14
Route::group(
    ['prefix' => 'comap_reporte', 'as' => 'comap_reporte', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Comap_reporte::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Comap_reporte::class, 'get_datos']);
        Route::get('get_valles', [App\Http\Controllers\Comap_reporte::class, 'get_valles']);
        Route::post('set_datos', [App\Http\Controllers\Comap_reporte::class, 'set_datos']);
    }
);
//15
Route::group(
    ['prefix' => 'comap', 'as' => 'comap', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Comap::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Comap::class, 'get_datos']);
        Route::post('get_beneficiarios', [App\Http\Controllers\Comap::class, 'get_beneficiarios']);
        Route::get('get_beneficiarios', [App\Http\Controllers\Comap::class, 'get_beneficiarios']);
        Route::post('filtrar', [App\Http\Controllers\Comap::class, 'filtrar']); //filtros de datagrid
        Route::post('update_datos', [App\Http\Controllers\Comap::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('save_datos', [App\Http\Controllers\Comap::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('destroy_datos', [App\Http\Controllers\Comap::class, 'destroy_datos'])->middleware('escritura'); //borras
    }
);
//16
Route::group(
    ['prefix' => 'kardex', 'as' => 'kardex', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Kardex::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Kardex::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Kardex::class, 'filtrar']); //filtros de datagrid
        Route::get('ver_kardex', [App\Http\Controllers\Kardex::class, 'ver_kardex']);
        Route::get('print_kardex', [App\Http\Controllers\Kardex::class, 'print_kardex']);
    }
);
//17
Route::group(
    ['prefix' => 'errores_lista', 'as' => 'errores_lista', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Errores_lista::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Errores_lista::class, 'get_datos']);
        Route::post('update_datos', [App\Http\Controllers\Errores_lista::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('save_datos', [App\Http\Controllers\Errores_lista::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('destroy_datos', [App\Http\Controllers\Errores_lista::class, 'destroy_datos'])->middleware('escritura'); //borras
        Route::post('filtrar', [App\Http\Controllers\Errores_lista::class, 'filtrar']); //filtros de datagrid
    }
);
//18
Route::group(
    ['prefix' => 'errores_revisar', 'as' => 'errores_revisar', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Errores_revisar::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Errores_revisar::class, 'get_datos']);
        Route::post('update_datos', [App\Http\Controllers\Errores_revisar::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('filtrar', [App\Http\Controllers\Errores_revisar::class, 'filtrar']); //filtros de datagrid
    }
);
//19
Route::group(
    ['prefix' => 'v_cargos', 'as' => 'v_cargos', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\V_cargos::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\V_cargos::class, 'get_datos']);
        Route::post('update_datos', [App\Http\Controllers\V_cargos::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('save_datos', [App\Http\Controllers\V_cargos::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('destroy_datos', [App\Http\Controllers\V_cargos::class, 'destroy_datos'])->middleware('escritura'); //borras
        Route::post('filtrar', [App\Http\Controllers\V_cargos::class, 'filtrar']); //filtros de datagrid
    }
);
//20
Route::group(
    ['prefix' => 'v_gestiones', 'as' => 'v_gestiones', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\V_gestiones::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\V_gestiones::class, 'get_datos']);
        Route::post('update_datos', [App\Http\Controllers\V_gestiones::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('save_datos', [App\Http\Controllers\V_gestiones::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('destroy_datos', [App\Http\Controllers\V_gestiones::class, 'destroy_datos'])->middleware('escritura'); //borras
        Route::post('filtrar', [App\Http\Controllers\V_gestiones::class, 'filtrar']); //filtros de datagrid
    }
);
Route::group(
    ['prefix' => 'v_autoridades', 'as' => 'v_autoridades', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\V_autoridades::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\V_autoridades::class, 'get_datos']);
        Route::post('update_cargo', [App\Http\Controllers\V_autoridades::class, 'update_cargo'])->middleware('escritura'); //actualiza
        Route::post('destroy_cargo', [App\Http\Controllers\V_autoridades::class, 'destroy_cargo'])->middleware('escritura'); //borras
        Route::post('filtrar', [App\Http\Controllers\V_autoridades::class, 'filtrar']); //filtros de datagrid
        Route::post('get_valles', [App\Http\Controllers\V_autoridades::class, 'get_valles']);
        Route::post('get_miembros', [App\Http\Controllers\V_autoridades::class, 'get_miembros']);
        Route::post('get_gestiones', [App\Http\Controllers\V_autoridades::class, 'get_gestiones']);
    }
);
Route::group(
    ['prefix' => 'mecom_obolos_valle', 'as' => 'mecom_obolos_valle', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_obolos_valle::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_obolos_valle::class, 'get_datos']);
        Route::post('save_datos', [App\Http\Controllers\Mecom_obolos_valle::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('update_datos', [App\Http\Controllers\Mecom_obolos_valle::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('destroy_datos', [App\Http\Controllers\Mecom_obolos_valle::class, 'destroy_datos'])->middleware('escritura'); //borras
    }
);
Route::group(
    ['prefix' => 'mecom_obolos_taller', 'as' => 'mecom_obolos_taller', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_obolos_taller::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_obolos_taller::class, 'get_datos']);
        Route::post('save_datos', [App\Http\Controllers\Mecom_obolos_taller::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('update_datos', [App\Http\Controllers\Mecom_obolos_taller::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('destroy_datos', [App\Http\Controllers\Mecom_obolos_taller::class, 'destroy_datos'])->middleware('escritura'); //borras
    }
);
Route::group(
    ['prefix' => 'mecom_reporte_pagosnet_banco', 'as' => 'mecom_reporte_pagosnet_banco', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_reporte_pagosnet_banco::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_reporte_pagosnet_banco::class, 'get_datos']);
        Route::post('set_datos', [App\Http\Controllers\Mecom_reporte_pagosnet_banco::class, 'set_datos']);
        Route::get('ver_archivo', [App\Http\Controllers\Mecom_reporte_pagosnet_banco::class, 'ver_archivo']);
    }
);
Route::group(
    ['prefix' => 'mecom_pagosnet_cuentas', 'as' => 'mecom_pagosnet_cuentas', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_pagosnet_cuentas::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_pagosnet_cuentas::class, 'get_datos']);
        Route::post('save_datos', [App\Http\Controllers\Mecom_pagosnet_cuentas::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('update_datos', [App\Http\Controllers\Mecom_pagosnet_cuentas::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('destroy_datos', [App\Http\Controllers\Mecom_pagosnet_cuentas::class, 'destroy_datos'])->middleware('escritura'); //borras
    }
);
Route::group(
    ['prefix' => 'mecom_descuentos', 'as' => 'mecom_descuentos', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_descuentos::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_descuentos::class, 'get_datos']);
        Route::post('save_datos', [App\Http\Controllers\Mecom_descuentos::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('update_datos', [App\Http\Controllers\Mecom_descuentos::class, 'update_datos'])->middleware('escritura'); //actualiza
    }
);
Route::group(
    ['prefix' => 'control_modificaciones', 'as' => 'control_modificaciones', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Control_modificaciones::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Control_modificaciones::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Control_modificaciones::class, 'filtrar']); //filtros de datagrid
        Route::post('estado', [App\Http\Controllers\Control_modificaciones::class, 'estado'])->middleware('escritura'); //cambia
    }
);
Route::group(
    ['prefix' => 'mecom_montos_tramites', 'as' => 'mecom_montos_tramites', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_montos_tramites::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_montos_tramites::class, 'get_datos']);
        Route::post('save_datos', [App\Http\Controllers\Mecom_montos_tramites::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('update_datos', [App\Http\Controllers\Mecom_montos_tramites::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('destroy_datos', [App\Http\Controllers\Mecom_montos_tramites::class, 'destroy_datos'])->middleware('escritura'); //borras
    }
);
Route::group(
    ['prefix' => 'mecom_pagos_extra_montos', 'as' => 'mecom_pagos_extra_montos', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_pagos_extra_montos::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_pagos_extra_montos::class, 'get_datos']);
        Route::post('save_datos', [App\Http\Controllers\Mecom_pagos_extra_montos::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('update_datos', [App\Http\Controllers\Mecom_pagos_extra_montos::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('destroy_datos', [App\Http\Controllers\Mecom_pagos_extra_montos::class, 'destroy_datos'])->middleware('escritura'); //borras
        Route::get('get_valles', [App\Http\Controllers\Mecom_pagos_extra_montos::class, 'get_valles']);
        Route::get('get_logias', [App\Http\Controllers\Mecom_pagos_extra_montos::class, 'get_logias']);
        Route::any('get_reporte', [App\Http\Controllers\Mecom_pagos_extra_montos::class, 'get_reporte']);
    }
);

Route::group(
    ['prefix' => 'logs_usuarios', 'as' => 'logs_usuarios', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Logs_usuarios::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Logs_usuarios::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Logs_usuarios::class, 'filtrar']); //filtros de datagrid
        Route::post('save_datos', [App\Http\Controllers\Logs_usuarios::class, 'save_datos'])->middleware('escritura'); //inserta
    }
);

Route::group(
    ['prefix' => 'tramites_ini_certificados', 'as' => 'tramites_ini_certificados', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_ini_certificados::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_ini_certificados::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_ini_certificados::class, 'filtrar']); //filtros de datagrid
        Route::post('update_ceremonia', [App\Http\Controllers\Tramites_ini_certificados::class, 'update_ceremonia'])->middleware('escritura'); //actualiza
        Route::post('get_logias', [App\Http\Controllers\Tramites_ini_certificados::class, 'get_logias']);
        Route::get('get_tramites', [App\Http\Controllers\Tramites_ini_certificados::class, 'get_tramites']);
    }
);
Route::group(
    ['prefix' => 'tramites_aum_certificados', 'as' => 'tramites_aum_certificados', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_aum_certificados::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_aum_certificados::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_aum_certificados::class, 'filtrar']); //filtros de datagrid
        Route::post('update_ceremonia', [App\Http\Controllers\Tramites_aum_certificados::class, 'update_ceremonia'])->middleware('escritura'); //actualiza
        Route::post('get_logias', [App\Http\Controllers\Tramites_aum_certificados::class, 'get_logias']);
        Route::get('get_tramites', [App\Http\Controllers\Tramites_aum_certificados::class, 'get_tramites']);
    }
);
Route::group(
    ['prefix' => 'tramites_exa_certificados', 'as' => 'tramites_exa_certificados', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_exa_certificados::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_exa_certificados::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_exa_certificados::class, 'filtrar']); //filtros de datagrid
        Route::post('update_ceremonia', [App\Http\Controllers\Tramites_exa_certificados::class, 'update_ceremonia'])->middleware('escritura'); //actualiza
        Route::post('get_logias', [App\Http\Controllers\Tramites_exa_certificados::class, 'get_logias']);
        Route::get('get_tramites', [App\Http\Controllers\Tramites_exa_certificados::class, 'get_tramites']);
    }
);
Route::group(
    ['prefix' => 'glb_comisiones', 'as' => 'glb_comisiones', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Glb_comisiones::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Glb_comisiones::class, 'get_datos']);
        Route::post('save_datos', [App\Http\Controllers\Glb_comisiones::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('update_datos', [App\Http\Controllers\Glb_comisiones::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('destroy_datos', [App\Http\Controllers\Glb_comisiones::class, 'destroy_datos'])->middleware('escritura'); //borras
    }
);
Route::group(
    ['prefix' => 'glb_cargos', 'as' => 'glb_cargos', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Glb_cargos::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Glb_cargos::class, 'get_datos']);
        Route::post('save_datos', [App\Http\Controllers\Glb_cargos::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('update_datos', [App\Http\Controllers\Glb_cargos::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('destroy_datos', [App\Http\Controllers\Glb_cargos::class, 'destroy_datos'])->middleware('escritura'); //borras
        Route::post('filtrar', [App\Http\Controllers\Glb_cargos::class, 'filtrar']); //filtros de datagrid
    }
);

Route::group(
    ['prefix' => 'glb_gestiones', 'as' => 'glb_gestiones', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Glb_gestiones::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Glb_gestiones::class, 'get_datos']);
        Route::post('save_datos', [App\Http\Controllers\Glb_gestiones::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('update_datos', [App\Http\Controllers\Glb_gestiones::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('destroy_datos', [App\Http\Controllers\Glb_gestiones::class, 'destroy_datos'])->middleware('escritura'); //borras
        Route::post('filtrar', [App\Http\Controllers\Glb_gestiones::class, 'filtrar']); //filtros de datagrid
    }
);
Route::group(
    ['prefix' => 'glb_miembros', 'as' => 'glb_miembros', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Glb_miembros::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Glb_miembros::class, 'get_datos']);
        Route::post('get_comisiones', [App\Http\Controllers\Glb_miembros::class, 'get_comisiones']);
        Route::post('get_miembros', [App\Http\Controllers\Glb_miembros::class, 'get_miembros']);
        Route::post('get_gestiones', [App\Http\Controllers\Glb_miembros::class, 'get_gestiones']);
        Route::post('update_cargo', [App\Http\Controllers\Glb_miembros::class, 'update_cargo'])->middleware('escritura'); //actualiza
        Route::post('destroy_cargo', [App\Http\Controllers\Glb_miembros::class, 'destroy_cargo'])->middleware('escritura'); //borras
        Route::post('filtrar', [App\Http\Controllers\Glb_miembros::class, 'filtrar']); //filtros de datagrid
    }
);
Route::group(
    ['prefix' => 'reporte_habiles_voto', 'as' => 'reporte_habiles_voto', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Reporte_habiles_voto::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Reporte_habiles_voto::class, 'get_datos']);
        Route::post('set_datos', [App\Http\Controllers\Reporte_habiles_voto::class, 'set_datos']);
    }
);
Route::group(
    ['prefix' => 'reporte_elegibles', 'as' => 'reporte_elegibles', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Reporte_elegibles::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Reporte_elegibles::class, 'get_datos']);
        Route::post('set_datos', [App\Http\Controllers\Reporte_elegibles::class, 'set_datos']);
    }
);
Route::group(
    ['prefix' => 'reporte_asistencia', 'as' => 'reporte_asistencia', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Reporte_asistencia::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Reporte_asistencia::class, 'get_datos']);
        Route::post('set_datos', [App\Http\Controllers\Reporte_asistencia::class, 'set_datos']);
        Route::any('gen_reporte', [App\Http\Controllers\Reporte_asistencia::class, 'gen_reporte']);///------ojala funcione
    }
);
Route::group(
    ['prefix' => 'tramites_valles_estado', 'as' => 'tramites_valles_estado', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_valles_estado::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_valles_estado::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_valles_estado::class, 'filtrar']); //filtros de datagrid
    }
);
Route::group(
    ['prefix' => 'v_membrecia', 'as' => 'v_membrecia', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\V_membrecia::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\V_membrecia::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\V_membrecia::class, 'filtrar']); //filtros de datagrid
    }
);
Route::group(
    ['prefix' => 'mecom_reporte_depositos_valles', 'as' => 'mecom_reporte_depositos_valles', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_reporte_depositos_valles::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_reporte_depositos_valles::class, 'get_datos']);
        Route::post('set_datos', [App\Http\Controllers\Mecom_reporte_depositos_valles::class, 'set_datos']);
    }
);
Route::group(
    ['prefix' => 'mecom_aprobar_depositos_valles', 'as' => 'mecom_aprobar_depositos_valles', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_aprobar_depositos_valles::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_aprobar_depositos_valles::class, 'get_datos']);
        Route::post('get_formularios', [App\Http\Controllers\Mecom_aprobar_depositos_valles::class, 'get_formularios']);
        Route::post('filtrar', [App\Http\Controllers\Mecom_aprobar_depositos_valles::class, 'filtrar']); //filtros de datagrid
        Route::post('send_formulario', [App\Http\Controllers\Mecom_aprobar_depositos_valles::class, 'send_formulario'])->middleware('escritura'); //actualiza
        Route::get('gen_formulario', [App\Http\Controllers\Mecom_aprobar_depositos_valles::class, 'gen_formulario']);
    }
);
//45
Route::group(
    ['prefix' => 'mecom_reporte_pagos_talleres', 'as' => 'mecom_reporte_pagos_talleres', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_reporte_pagos_talleres::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_reporte_pagos_talleres::class, 'get_datos']);
        Route::post('set_datos', [App\Http\Controllers\Mecom_reporte_pagos_talleres::class, 'set_datos']);
    }
);
//46
Route::group(
    ['prefix' => 'v_afiliados', 'as' => 'v_afiliados', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\V_afiliados::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\V_afiliados::class, 'get_datos']);
        Route::post('set_datos', [App\Http\Controllers\V_afiliados::class, 'set_datos']);
        Route::post('get_valles', [App\Http\Controllers\V_afiliados::class, 'get_valles']);
        Route::get('get_logias', [App\Http\Controllers\V_afiliados::class, 'get_logias']);
    }
);
//47
Route::group(
    ['prefix' => 'regularizacion_registro', 'as' => 'regularizacion_registro', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Regularizacion_registro::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Regularizacion_registro::class, 'get_datos']);
        Route::post('save_datos', [App\Http\Controllers\Regularizacion_registro::class, 'save_datos'])->middleware('escritura'); //inserta
        Route::post('update_datos', [App\Http\Controllers\Regularizacion_registro::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('destroy_datos', [App\Http\Controllers\Regularizacion_registro::class, 'destroy_datos'])->middleware('escritura'); //borras
        Route::post('filtrar', [App\Http\Controllers\Regularizacion_registro::class, 'filtrar']); //filtros de datagrid
    }
);
//48
Route::group(
    ['prefix' => 'modificacion_registro', 'as' => 'modificacion_registro', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Modificacion_registro::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Modificacion_registro::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Modificacion_registro::class, 'filtrar']); //filtros de datagrid
        Route::post('update_datos', [App\Http\Controllers\Modificacion_registro::class, 'update_datos'])->middleware('escritura'); //actualiza
        Route::post('estado', [App\Http\Controllers\Modificacion_registro::class, 'estado'])->middleware('escritura'); //cambiadato
        Route::get('get_form', [App\Http\Controllers\Modificacion_registro::class, 'get_form']);
    }
);
//49
Route::group(
    ['prefix' => 'tramites_ini_ver_depositos', 'as' => 'tramites_ini_ver_depositos', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_ini_ver_depositos::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_ini_ver_depositos::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_ini_ver_depositos::class, 'filtrar']); //filtros de datagrid
        Route::get('get_tramite', [App\Http\Controllers\Tramites_ini_ver_depositos::class, 'get_tramite']);
    }
);
//50
Route::group(
    ['prefix' => 'tramites_aum_ver_depositos', 'as' => 'tramites_aum_ver_depositos', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_aum_ver_depositos::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_aum_ver_depositos::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_aum_ver_depositos::class, 'filtrar']); //filtros de datagrid
        Route::get('get_tramite', [App\Http\Controllers\Tramites_aum_ver_depositos::class, 'get_tramite']);
    }
);
//51
Route::group(
    ['prefix' => 'tramites_exa_ver_depositos', 'as' => 'tramites_exa_ver_depositos', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_exa_ver_depositos::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_exa_ver_depositos::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_exa_ver_depositos::class, 'filtrar']); //filtros de datagrid
        Route::get('get_tramite', [App\Http\Controllers\Tramites_exa_ver_depositos::class, 'get_tramite']);
    }
);
//52
Route::group(
    ['prefix' => 'tramites_afilia_ver_depositos', 'as' => 'tramites_afilia_ver_depositos', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_afilia_ver_depositos::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_afilia_ver_depositos::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_afilia_ver_depositos::class, 'filtrar']); //filtros de datagrid
    }
);
//53
Route::group(
    ['prefix' => 'tramites_reincorp_ver_depositos', 'as' => 'tramites_reincorp_ver_depositos', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_reincorp_ver_depositos::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_reincorp_ver_depositos::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_reincorp_ver_depositos::class, 'filtrar']); //filtros de datagrid
    }
);
//54
Route::group(
    ['prefix' => 'mecom_estado_obolos', 'as' => 'mecom_estado_obolos', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_estado_obolos::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_estado_obolos::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Mecom_estado_obolos::class, 'filtrar']); //filtros de datagrid

    }
);
//55
Route::group(
    ['prefix' => 'tramites_reincorporaciones', 'as' => 'tramites_reincorporaciones', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_reincorporaciones::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_reincorporaciones::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_reincorporaciones::class, 'filtrar']); //filtros de datagrid
        Route::post('get_miembros', [App\Http\Controllers\Tramites_reincorporaciones::class, 'get_miembros']);
        Route::post('save_tramite', [App\Http\Controllers\Tramites_reincorporaciones::class, 'save_tramite'])->middleware('escritura'); //inserta
        Route::post('cambia_tramite', [App\Http\Controllers\Tramites_reincorporaciones::class, 'cambia_tramite'])->middleware('escritura');
        Route::post('unset_tramite', [App\Http\Controllers\Tramites_reincorporaciones::class, 'unset_tramite'])->middleware('escritura'); //inserta
        Route::post('cambia_datos', [App\Http\Controllers\Tramites_reincorporaciones::class, 'cambia_datos'])->middleware('escritura'); //aun falta
        Route::post('registra_pago', [App\Http\Controllers\Tramites_reincorporaciones::class, 'registra_pago'])->middleware('escritura');
        Route::get('gen_certificado', [App\Http\Controllers\Tramites_reincorporaciones::class, 'gen_certificado']);
    }
);
//56
Route::group(
    ['prefix' => 'asistencias_estado', 'as' => 'asistencias_estado', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Asistencias_estado::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Asistencias_estado::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Asistencias_estado::class, 'filtrar']); //filtros de datagrid
    }
);
//57
Route::group(
    ['prefix' => 'asistencias_registro', 'as' => 'asistencias_registro', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Asistencias_registro::class, 'index']);
        Route::post('get_dias', [App\Http\Controllers\Asistencias_registro::class, 'get_dias']);
        Route::post('get_datos', [App\Http\Controllers\Asistencias_registro::class, 'get_datos']);
        Route::post('get_visitas', [App\Http\Controllers\Asistencias_registro::class, 'get_visitas']);
        Route::post('get_miembros', [App\Http\Controllers\Asistencias_registro::class, 'get_miembros']);
        Route::post('get_oficiales', [App\Http\Controllers\Asistencias_registro::class, 'get_oficiales']);
        Route::post('filtrar', [App\Http\Controllers\Asistencias_registro::class, 'filtrar']); //filtros de datagrid
        Route::post('filter_diatenida', [App\Http\Controllers\Asistencias_registro::class, 'filter_diatenida']); //filtros de datagrid
        Route::post('filter_taller', [App\Http\Controllers\Asistencias_registro::class, 'filter_taller']); //filtros de datagrid
        Route::get('get_datasis', [App\Http\Controllers\Asistencias_registro::class, 'get_datasis']);
        Route::post('update_datasis', [App\Http\Controllers\Asistencias_registro::class, 'update_datasis'])->middleware('escritura'); //inserta
        Route::post('update_asis', [App\Http\Controllers\Asistencias_registro::class, 'update_asis'])->middleware('escritura'); //inserta
        Route::post('add_visita', [App\Http\Controllers\Asistencias_registro::class, 'add_visita'])->middleware('escritura'); //inserta
        Route::post('quitar_visita', [App\Http\Controllers\Asistencias_registro::class, 'quitar_visita'])->middleware('escritura'); //
        Route::post('set_oficialpt', [App\Http\Controllers\Asistencias_registro::class, 'set_oficialpt'])->middleware('escritura'); //
        Route::get('gen_planilla', [App\Http\Controllers\Asistencias_registro::class, 'gen_planilla']);
    }
);
//58
Route::group(
    ['prefix' => 'asistencias_extratemplos', 'as' => 'asistencias_extratemplos', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Asistencias_extratemplos::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Asistencias_extratemplos::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Asistencias_extratemplos::class, 'filtrar']); //filtros de datagrid
        Route::post('filter_taller', [App\Http\Controllers\Asistencias_extratemplos::class, 'filter_taller']); //filtros de datagrid
        Route::post('filter_diatenida', [App\Http\Controllers\Asistencias_extratemplos::class, 'filter_diatenida']); //filtros de datagrid
        Route::post('get_dias', [App\Http\Controllers\Asistencias_extratemplos::class, 'get_dias']);
        Route::post('update_asiset', [App\Http\Controllers\Asistencias_extratemplos::class, 'update_asiset'])->middleware('escritura'); //inserta
        Route::post('save_extra', [App\Http\Controllers\Asistencias_extratemplos::class, 'save_extra'])->middleware('escritura'); //inserta
        Route::post('update_datasiset', [App\Http\Controllers\Asistencias_extratemplos::class, 'update_datasiset'])->middleware('escritura'); //
        Route::post('destroy_dataet', [App\Http\Controllers\Asistencias_extratemplos::class, 'destroy_dataet'])->middleware('escritura'); //
        Route::get('gen_planilla', [App\Http\Controllers\Asistencias_extratemplos::class, 'gen_planilla']);
    }
);
//59
Route::group(
    ['prefix' => 'logia_oficiales', 'as' => 'logia_oficiales', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Logia_oficiales::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Logia_oficiales::class, 'get_datos']);
        Route::post('get_miembros', [App\Http\Controllers\Logia_oficiales::class, 'get_miembros']);
        Route::post('filtrar', [App\Http\Controllers\Logia_oficiales::class, 'filtrar']);
        Route::post('mostrar', [App\Http\Controllers\Logia_oficiales::class, 'mostrar']);
        Route::post('update_cargo', [App\Http\Controllers\Logia_oficiales::class, 'update_cargo'])->middleware('escritura'); //inserta
        Route::post('destroy_cargo', [App\Http\Controllers\Logia_oficiales::class, 'destroy_cargo'])->middleware('escritura'); //inserta
    }
);
//60
Route::group(
    ['prefix' => 'asistencias_extras', 'as' => 'asistencias_extras', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Asistencias_extras::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Asistencias_extras::class, 'get_datos']);
        Route::post('get_visitas', [App\Http\Controllers\Asistencias_extras::class, 'get_visitas']);
        Route::post('filtrar', [App\Http\Controllers\Asistencias_extras::class, 'filtrar']); //filtros de datagrid
        Route::post('set_visita', [App\Http\Controllers\Asistencias_extras::class, 'set_visita'])->middleware('escritura'); //inserta
        Route::post('destroy_visita', [App\Http\Controllers\Asistencias_extras::class, 'destroy_visita'])->middleware('escritura'); //inserta
    }
);
//61
Route::group(
    ['prefix' => 'asistencias_administracion', 'as' => 'asistencias_administracion', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Asistencias_administracion::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Asistencias_administracion::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Asistencias_administracion::class, 'filtrar']); //filtros de datagrid
        Route::post('filter_taller', [App\Http\Controllers\Asistencias_administracion::class, 'filter_taller']); //filtros de datagrid
        Route::post('get_miembros', [App\Http\Controllers\Asistencias_administracion::class, 'get_miembros']);
        Route::post('get_dias', [App\Http\Controllers\Asistencias_administracion::class, 'get_dias']);
        Route::post('filter_diatenida', [App\Http\Controllers\Asistencias_administracion::class, 'filter_diatenida']); //filtros de datagrid
        Route::post('update_asis', [App\Http\Controllers\Asistencias_administracion::class, 'update_asis'])->middleware('escritura');
        Route::post('set_asistencia', [App\Http\Controllers\Asistencias_administracion::class, 'set_asistencia'])->middleware('escritura');
        Route::get('get_datasis', [App\Http\Controllers\Asistencias_administracion::class, 'get_datasis']);
        Route::post('update_datasis', [App\Http\Controllers\Asistencias_administracion::class, 'update_datasis'])->middleware('escritura');
        Route::post('save_fechan', [App\Http\Controllers\Asistencias_administracion::class, 'save_fechan'])->middleware('escritura');
        Route::post('destroy_fechan', [App\Http\Controllers\Asistencias_administracion::class, 'destroy_fechan'])->middleware('escritura');
    }
);
//62
Route::group(
    ['prefix' => 'tramites_afilia_registro', 'as' => 'tramites_afilia_registro', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_afilia_registro::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_afilia_registro::class, 'get_datos']);
        Route::post('get_miembros', [App\Http\Controllers\Tramites_afilia_registro::class, 'get_miembros']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_afilia_registro::class, 'filtrar']); //filtros de datagrid
        Route::post('save_tramite', [App\Http\Controllers\Tramites_afilia_registro::class, 'save_tramite'])->middleware('escritura');
        Route::post('unset_tramite', [App\Http\Controllers\Tramites_afilia_registro::class, 'unset_tramite'])->middleware('escritura');
        Route::post('registra_pago', [App\Http\Controllers\Tramites_afilia_registro::class, 'registra_pago'])->middleware('escritura');
        Route::post('cambia_datos', [App\Http\Controllers\Tramites_afilia_registro::class, 'cambia_datos'])->middleware('escritura');
    }
);
//63
Route::group(
    ['prefix' => 'mecom_formularios_ver', 'as' => 'mecom_formularios_ver', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_formularios_ver::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_formularios_ver::class, 'get_datos']);
        Route::post('get_miembros', [App\Http\Controllers\Mecom_formularios_ver::class, 'get_miembros']);
        Route::post('get_formularios', [App\Http\Controllers\Mecom_formularios_ver::class, 'get_formularios']);
        Route::post('filtrar', [App\Http\Controllers\Mecom_formularios_ver::class, 'filtrar']); //filtros de datagrid
        Route::post('set_pagomiembro', [App\Http\Controllers\Mecom_formularios_ver::class, 'set_pagomiembro'])->middleware('escritura');
        Route::get('gen_formulario', [App\Http\Controllers\Mecom_formularios_ver::class, 'gen_formulario']);
    }
);
//64
Route::group(
    ['prefix' => 'mecom_formularios_pagar', 'as' => 'mecom_formularios_pagar', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_formularios_pagar::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_formularios_pagar::class, 'get_datos']);
        Route::post('get_miembros', [App\Http\Controllers\Mecom_formularios_pagar::class, 'get_miembros']);
        Route::post('get_formularios', [App\Http\Controllers\Mecom_formularios_pagar::class, 'get_formularios']);
        Route::post('filtrar', [App\Http\Controllers\Mecom_formularios_pagar::class, 'filtrar']); //filtros de datagrid
        Route::post('createqr_formaporte', [App\Http\Controllers\Mecom_formularios_pagar::class, 'createqr_formaporte'])->middleware('escritura');
        Route::post('set_pagomiembro', [App\Http\Controllers\Mecom_formularios_pagar::class, 'set_pagomiembro'])->middleware('escritura');
        Route::post('remove_obolo', [App\Http\Controllers\Mecom_formularios_pagar::class, 'remove_obolo'])->middleware('escritura');
        Route::get('gen_qrformulario', [App\Http\Controllers\Mecom_formularios_pagar::class, 'gen_qrformulario']);
        Route::post('anular_formulario', [App\Http\Controllers\Mecom_formularios_pagar::class, 'anular_formulario']);
        Route::get('get_datos_qr', [App\Http\Controllers\Mecom_formularios_pagar::class, 'get_datos_qr']);
        Route::post('get_pago_qr', [App\Http\Controllers\Mecom_formularios_pagar::class, 'get_pago_qr']);
        Route::get('gen_recibo', [App\Http\Controllers\Mecom_formularios_pagar::class, 'gen_recibo']);
    }
);
//65
Route::group(
    ['prefix' => 'mecom_formularios_revisar', 'as' => 'mecom_formularios_revisar', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_formularios_revisar::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_formularios_revisar::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Mecom_formularios_revisar::class, 'filtrar']); //filtros de datagrid
        Route::post('get_formularios', [App\Http\Controllers\Mecom_formularios_revisar::class, 'get_formularios']);
        Route::get('gen_formulario', [App\Http\Controllers\Mecom_formularios_revisar::class, 'gen_formulario']);
        Route::get('gen_reporte', [App\Http\Controllers\Mecom_formularios_revisar::class, 'gen_reporte']);
    }
);
//66
Route::group(
    ['prefix' => 'mecom_webobolos_repo', 'as' => 'mecom_webobolos_repo', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_webobolos_repo::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_webobolos_repo::class, 'get_datos']);
        Route::post('set_datos', [App\Http\Controllers\Mecom_webobolos_repo::class, 'set_datos']); //filtros de datagrid
    }
);
//67
Route::group(
    ['prefix' => 'mecom_formularios_aprobar', 'as' => 'mecom_formularios_aprobar', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_formularios_aprobar::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_formularios_aprobar::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Mecom_formularios_aprobar::class, 'filtrar']); //filtros de datagrid
        Route::post('get_formularios', [App\Http\Controllers\Mecom_formularios_aprobar::class, 'get_formularios']);
        Route::get('gen_formulario', [App\Http\Controllers\Mecom_formularios_aprobar::class, 'gen_formulario']);
        Route::post('send_formulario', [App\Http\Controllers\Mecom_formularios_aprobar::class, 'send_formulario']);
        Route::get('gen_reporte', [App\Http\Controllers\Mecom_formularios_aprobar::class, 'gen_reporte']);
        Route::post('recal_formulario', [App\Http\Controllers\Mecom_formularios_aprobar::class, 'recal_formulario'])->middleware('escritura');
        Route::post('anula_formulario', [App\Http\Controllers\Mecom_formularios_aprobar::class, 'anula_formulario'])->middleware('escritura');
    }
);
//68
Route::group(
    ['prefix' => 'mecom_obolos_repo', 'as' => 'mecom_obolos_repo', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_obolos_repo::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_obolos_repo::class, 'get_datos']);
        Route::post('set_datos', [App\Http\Controllers\Mecom_obolos_repo::class, 'set_datos']); //filtros de datagrid
    }
);
//69
Route::group(
    ['prefix' => 'mecom_obolos_admin', 'as' => 'mecom_obolos_admin', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_obolos_admin::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_obolos_admin::class, 'get_datos']);
        Route::post('get_obolos', [App\Http\Controllers\Mecom_obolos_admin::class, 'get_obolos']);
        Route::get('get_obolo', [App\Http\Controllers\Mecom_obolos_admin::class, 'get_obolo']);
        Route::post('filtrar', [App\Http\Controllers\Mecom_obolos_admin::class, 'filtrar']); //filtro
        Route::post('set_obolo', [App\Http\Controllers\Mecom_obolos_admin::class, 'set_obolo'])->middleware('escritura');
    }
);
//70
Route::group(
    ['prefix' => 'mecom_descuentos_repo', 'as' => 'mecom_descuentos_repo', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_descuentos_repo::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_descuentos_repo::class, 'get_datos']);
        Route::post('set_datos', [App\Http\Controllers\Mecom_descuentos_repo::class, 'set_datos'])->middleware('escritura');
    }
);
//71
Route::group(
    ['prefix' => 'mecom_pagosnet_repo_talleres', 'as' => 'mecom_pagosnet_repo_talleres', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_pagosnet_repo_talleres::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_pagosnet_repo_talleres::class, 'get_datos']);
        Route::post('set_datos', [App\Http\Controllers\Mecom_pagosnet_repo_talleres::class, 'set_datos'])->middleware('escritura');
    }
);
//72
Route::group(
    ['prefix' => 'mecom_pagosnet_repo_conta', 'as' => 'mecom_pagosnet_repo_conta', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_pagosnet_repo_conta::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_pagosnet_repo_conta::class, 'get_datos']);
        Route::post('set_datos', [App\Http\Controllers\Mecom_pagosnet_repo_conta::class, 'set_datos']);
        Route::get('gen_formulario', [App\Http\Controllers\Mecom_pagosnet_repo_conta::class, 'gen_formulario']);
    }
);
//73
Route::group(
    ['prefix' => 'mecom_pagos_extra_pagarqr', 'as' => 'mecom_pagos_extra_pagarqr', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_pagos_extra_pagarqr::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_pagos_extra_pagarqr::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Mecom_pagos_extra_pagarqr::class, 'filtrar']); //filtro
        Route::post('exe_pago', [App\Http\Controllers\Mecom_pagos_extra_pagarqr::class, 'exe_pago']);
        Route::post('get_datos_pagos', [App\Http\Controllers\Mecom_pagos_extra_pagarqr::class, 'get_datos_pagos']);
    }
);
//74
Route::group(
    ['prefix' => 'tramites_afilia_depositosqr', 'as' => 'tramites_afilia_depositosqr', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_afilia_depositosqr::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_afilia_depositosqr::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_afilia_depositosqr::class, 'filtrar']); //filtro
        Route::post('get_pago_datos', [App\Http\Controllers\Tramites_afilia_depositosqr::class, 'get_pago_datos']);
        Route::get('get_values', [App\Http\Controllers\Tramites_afilia_depositosqr::class, 'get_values']);
        //Route::get('gen_certificado', [App\Http\Controllers\Tramites_afilia_depositosqr::class, 'gen_certificado']);
    }
);
//75
Route::group(
    ['prefix' => 'tramites_reincorporacion_depositosqr', 'as' => 'tramites_reincorporacion_depositosqr', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_reincorporacion_depositosqr::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_reincorporacion_depositosqr::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_reincorporacion_depositosqr::class, 'filtrar']); //filtro
        Route::post('get_pago_datos', [App\Http\Controllers\Tramites_reincorporacion_depositosqr::class, 'get_pago_datos']);
        Route::get('get_values', [App\Http\Controllers\Tramites_reincorporacion_depositosqr::class, 'get_values']);
        Route::get('gen_certificado', [App\Http\Controllers\Tramites_reincorporacion_depositosqr::class, 'gen_certificado']);
    }
);
//76
Route::group(
    ['prefix' => 'tramites_ini_ver_certificados', 'as' => 'tramites_ini_ver_certificados', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_ini_ver_certificados::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_ini_ver_certificados::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_ini_ver_certificados::class, 'filtrar']); //filtros de datagrid
        Route::post('get_logias', [App\Http\Controllers\Tramites_ini_ver_certificados::class, 'get_logias']);
        Route::get('gen_reporte', [App\Http\Controllers\Tramites_ini_ver_certificados::class, 'gen_reporte']);
    }
);
//77
Route::group(
    ['prefix' => 'tramites_aum_ver_certificados', 'as' => 'tramites_aum_ver_certificados', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_aum_ver_certificados::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_aum_ver_certificados::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_aum_ver_certificados::class, 'filtrar']); //filtros de datagrid
        Route::post('get_logias', [App\Http\Controllers\Tramites_aum_ver_certificados::class, 'get_logias']);
        Route::get('gen_reporte', [App\Http\Controllers\Tramites_aum_ver_certificados::class, 'gen_reporte']);
    }
);
//78
Route::group(
    ['prefix' => 'tramites_exa_ver_certificados', 'as' => 'tramites_exa_ver_certificados', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_exa_ver_certificados::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_exa_ver_certificados::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_exa_ver_certificados::class, 'filtrar']); //filtros de datagrid
        Route::post('get_logias', [App\Http\Controllers\Tramites_exa_ver_certificados::class, 'get_logias']);
        Route::get('gen_reporte', [App\Http\Controllers\Tramites_exa_ver_certificados::class, 'gen_reporte']);
    }
);
//79
Route::group(
    ['prefix' => 'tramites_ini_cero', 'as' => 'tramites_ini_cero', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_ini_cero::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_ini_cero::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_ini_cero::class, 'filtrar']); //filtros de datagrid
        Route::get('get_tramite', [App\Http\Controllers\Tramites_ini_cero::class, 'get_tramite']);
        Route::get('get_nlogia', [App\Http\Controllers\Tramites_ini_cero::class, 'get_nlogia']);
        Route::post('save_tramite', [App\Http\Controllers\Tramites_ini_cero::class, 'save_tramite'])->middleware('escritura');
        Route::post('update_tramite', [App\Http\Controllers\Tramites_ini_cero::class, 'update_tramite'])->middleware('escritura');
    }
);
//80
Route::group(
    ['prefix' => 'tramites_ini_uno', 'as' => 'tramites_ini_uno', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_ini_uno::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_ini_uno::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_ini_uno::class, 'filtrar']); //filtros de datagrid
        Route::get('get_tramite', [App\Http\Controllers\Tramites_ini_uno::class, 'get_tramite']);
        Route::post('update_tramite', [App\Http\Controllers\Tramites_ini_uno::class, 'update_tramite'])->middleware('escritura');
    }
);
//81
Route::group(
    ['prefix' => 'tramites_ini_dos', 'as' => 'tramites_ini_dos', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_ini_dos::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_ini_dos::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_ini_dos::class, 'filtrar']); //filtros de datagrid
        Route::post('get_circular', [App\Http\Controllers\Tramites_ini_dos::class, 'get_circular']);
    }
);
//82
Route::group(
    ['prefix' => 'tramites_ini_circulares', 'as' => 'tramites_ini_circulares', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_ini_circulares::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_ini_circulares::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_ini_circulares::class, 'filtrar']); //filtros de datagrid
        Route::post('ver_circular', [App\Http\Controllers\Tramites_ini_circulares::class, 'gen_circular']);
    }
);
//83
Route::group(
    ['prefix' => 'tramites_aum_cero', 'as' => 'tramites_aum_cero', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_aum_cero::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_aum_cero::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_aum_cero::class, 'filtrar']); //filtros de datagrid
        Route::get('get_tramite', [App\Http\Controllers\Tramites_aum_cero::class, 'get_tramite']);
        Route::post('save_tramite', [App\Http\Controllers\Tramites_aum_cero::class, 'save_tramite'])->middleware('escritura');
    }
);
//84
Route::group(
    ['prefix' => 'tramites_aum_uno', 'as' => 'tramites_aum_uno', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_aum_uno::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_aum_uno::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_aum_uno::class, 'filtrar']); //filtros de datagrid
        Route::get('get_tramite', [App\Http\Controllers\Tramites_aum_uno::class, 'get_tramite']);
        Route::post('update_tramite', [App\Http\Controllers\Tramites_aum_uno::class, 'update_tramite'])->middleware('escritura');
    }
);
//85
Route::group(
    ['prefix' => 'tramites_exa_cero', 'as' => 'tramites_exa_cero', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_exa_cero::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_exa_cero::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_exa_cero::class, 'filtrar']); //filtros de datagrid
        Route::get('get_tramite', [App\Http\Controllers\Tramites_exa_cero::class, 'get_tramite']);
        Route::post('save_tramite', [App\Http\Controllers\Tramites_exa_cero::class, 'save_tramite'])->middleware('escritura');
    }
);
//86
Route::group(
    ['prefix' => 'tramites_exa_uno', 'as' => 'tramites_exa_uno', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_exa_uno::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_exa_uno::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_exa_uno::class, 'filtrar']); //filtros de datagrid
        Route::get('get_tramite', [App\Http\Controllers\Tramites_exa_uno::class, 'get_tramite']);
        Route::post('update_tramite', [App\Http\Controllers\Tramites_exa_uno::class, 'update_tramite'])->middleware('escritura');
    }
);
//87
Route::group(
    ['prefix' => 'tramites_aum_dos', 'as' => 'tramites_aum_dos', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_aum_dos::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_aum_dos::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_aum_dos::class, 'filtrar']); //filtros de datagrid
        Route::get('get_tramites', [App\Http\Controllers\Tramites_aum_dos::class, 'get_tramites']);
        Route::post('update_ceremonia', [App\Http\Controllers\Tramites_aum_dos::class, 'update_ceremonia'])->middleware('escritura');
    }
);
//88
Route::group(
    ['prefix' => 'tramites_exa_dos', 'as' => 'tramites_exa_dos', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_exa_dos::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_exa_dos::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_exa_dos::class, 'filtrar']); //filtros de datagrid
        Route::get('get_tramites', [App\Http\Controllers\Tramites_exa_dos::class, 'get_tramites']);
        Route::post('update_ceremonia', [App\Http\Controllers\Tramites_exa_dos::class, 'update_ceremonia'])->middleware('escritura');
    }
);
//89
Route::group(
    ['prefix' => 'tramites_aum_pagarqr', 'as' => 'tramites_aum_pagarqr', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_aum_pagarqr::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_aum_pagarqr::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_aum_pagarqr::class, 'filtrar']); //filtros de datagrid
        Route::get('get_ceremonia', [App\Http\Controllers\Tramites_aum_pagarqr::class, 'get_ceremonia']);
        Route::post('get_datos_pagos', [App\Http\Controllers\Tramites_aum_pagarqr::class, 'get_datos_pagos']);
    }
);
//90
Route::group(
    ['prefix' => 'tramites_exa_pagarqr', 'as' => 'tramites_exa_pagarqr', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_exa_pagarqr::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_exa_pagarqr::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_exa_pagarqr::class, 'filtrar']); //filtros de datagrid
        Route::get('get_ceremonia', [App\Http\Controllers\Tramites_exa_pagarqr::class, 'get_ceremonia']);
        Route::post('get_datos_pagos', [App\Http\Controllers\Tramites_exa_pagarqr::class, 'get_datos_pagos']);
    }
);
//91
Route::group(
    ['prefix' => 'tramites_aum_cuatro', 'as' => 'tramites_aum_cuatro', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_aum_cuatro::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_aum_cuatro::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_aum_cuatro::class, 'filtrar']); //filtros de datagrid
        Route::get('get_ceremonia', [App\Http\Controllers\Tramites_aum_cuatro::class, 'get_ceremonia']);
        Route::post('update_ceremonia', [App\Http\Controllers\Tramites_aum_cuatro::class, 'update_ceremonia'])->middleware('escritura');
    }
);
//92
Route::group(
    ['prefix' => 'tramites_exa_cuatro', 'as' => 'tramites_exa_cuatro', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_exa_cuatro::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_exa_cuatro::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_exa_cuatro::class, 'filtrar']); //filtros de datagrid
        Route::get('get_ceremonia', [App\Http\Controllers\Tramites_exa_cuatro::class, 'get_ceremonia']);
        Route::post('update_ceremonia', [App\Http\Controllers\Tramites_exa_cuatro::class, 'update_ceremonia'])->middleware('escritura');
    }
);
//93
Route::group(
    ['prefix' => 'tramites_ini_tres', 'as' => 'tramites_ini_tres', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_ini_tres::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_ini_tres::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_ini_tres::class, 'filtrar']); //filtros de datagrid
        Route::get('get_tramite', [App\Http\Controllers\Tramites_ini_tres::class, 'get_tramite']);
        Route::post('update_tramite', [App\Http\Controllers\Tramites_ini_tres::class, 'update_tramite'])->middleware('escritura');
        Route::post('save_observacion', [App\Http\Controllers\Tramites_ini_tres::class, 'save_observacion'])->middleware('escritura');
    }
);
//94
Route::group(
    ['prefix' => 'tramites_ini_cuatro', 'as' => 'tramites_ini_cuatro', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_ini_cuatro::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_ini_cuatro::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_ini_cuatro::class, 'filtrar']); //filtros de datagrid
        Route::get('get_tramite', [App\Http\Controllers\Tramites_ini_cuatro::class, 'get_tramite']);
        Route::post('update_tramite', [App\Http\Controllers\Tramites_ini_cuatro::class, 'update_tramite'])->middleware('escritura');
        Route::post('save_observacion', [App\Http\Controllers\Tramites_ini_tres::class, 'save_observacion'])->middleware('escritura');
    }
);
//95
Route::group(
    ['prefix' => 'tramites_ini_cinco', 'as' => 'tramites_ini_cinco', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_ini_cinco::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_ini_cinco::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_ini_cinco::class, 'filtrar']); //filtros de datagrid
        Route::get('get_tramite', [App\Http\Controllers\Tramites_ini_cinco::class, 'get_tramite']);
        Route::post('update_tramite', [App\Http\Controllers\Tramites_ini_cinco::class, 'update_tramite'])->middleware('escritura');
    }
);
//96
Route::group(
    ['prefix' => 'tramites_ini_pagarqr', 'as' => 'tramites_ini_pagarqr', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_ini_pagarqr::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_ini_pagarqr::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_ini_pagarqr::class, 'filtrar']); //filtros de datagrid
        Route::get('get_ceremonia', [App\Http\Controllers\Tramites_ini_pagarqr::class, 'get_ceremonia']);
        Route::post('get_datos_pagos', [App\Http\Controllers\Tramites_ini_pagarqr::class, 'get_datos_pagos']);
    }
);
//97
Route::group(
    ['prefix' => 'tramites_ini_seis', 'as' => 'tramites_ini_seis', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_ini_seis::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_ini_seis::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_ini_seis::class, 'filtrar']); //filtros de datagrid
        Route::get('get_tramite', [App\Http\Controllers\Tramites_ini_seis::class, 'get_tramite']);
        Route::post('update_tramite', [App\Http\Controllers\Tramites_ini_seis::class, 'update_tramite'])->middleware('escritura');
    }
);
//98
Route::group(
    ['prefix' => 'tramites_ini_siete', 'as' => 'tramites_ini_siete', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_ini_siete::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_ini_siete::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_ini_siete::class, 'filtrar']); //filtros de datagrid
        Route::get('gen_reporte', [App\Http\Controllers\Tramites_ini_siete::class, 'get_reporte']);
    }
);
//99
Route::group(
    ['prefix' => 'tramites_afilia_aprobar', 'as' => 'tramites_afilia_aprobar', 'middleware' => ['auth', 'permission', 'sites']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_afilia_aprobar::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_afilia_aprobar::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_afilia_aprobar::class, 'filtrar']); //filtros de datagrid
        Route::post('cambia_datos', [App\Http\Controllers\Tramites_afilia_aprobar::class, 'cambia_datos'])->middleware('escritura');
    }
);
//100
Route::group(
    ['prefix' => 'tramites_aum_estado', 'as' => 'tramites_aum_estado', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_aum_estado::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_aum_estado::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_aum_estado::class, 'filtrar']); //filtros de datagrid
        Route::get('get_tramite', [App\Http\Controllers\Tramites_aum_estado::class, 'get_tramite']);
        Route::post('update_tramite', [App\Http\Controllers\Tramites_aum_estado::class, 'update_tramite'])->middleware('escritura');
    }
);
//101
Route::group(
    ['prefix' => 'tramites_ini_observaciones', 'as' => 'tramites_ini_observaciones', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_ini_observaciones::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_ini_observaciones::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_ini_observaciones::class, 'filtrar']); //filtros de datagrid
        Route::post('get_tramites', [App\Http\Controllers\Tramites_ini_observaciones::class, 'get_tramites']);
        Route::post('save_tramite', [App\Http\Controllers\Tramites_ini_observaciones::class, 'save_tramite'])->middleware('escritura');
        Route::post('delete_obs', [App\Http\Controllers\Tramites_ini_observaciones::class, 'delete_tramite'])->middleware('escritura');
    }
);
//102
Route::group(
    ['prefix' => 'miembros_meritos', 'as' => 'miembros_meritos', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Miembros_meritos::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Miembros_meritos::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Miembros_meritos::class, 'filtrar']); //filtros de datagrid
        Route::post('get_meritos', [App\Http\Controllers\Miembros_meritos::class, 'get_meritos']);
        Route::post('save_merito', [App\Http\Controllers\Miembros_meritos::class, 'save_merito'])->middleware('escritura');
        Route::post('delete_merito', [App\Http\Controllers\Miembros_meritos::class, 'delete_merito'])->middleware('escritura');
    }
);
//103
Route::group(
    ['prefix' => 'tramites_ini_observaciones_listado', 'as' => 'tramites_ini_observaciones_listado', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_ini_observaciones_listado::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_ini_observaciones_listado::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_ini_observaciones_listado::class, 'filtrar']); //filtros de datagrid
        Route::post('get_tramites', [App\Http\Controllers\Tramites_ini_observaciones_listado::class, 'get_tramites']);
    }
);
//104
Route::group(
    ['prefix' => 'miembros_meritos_listado', 'as' => 'miembros_meritos_listado', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Miembros_meritos_listado::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Miembros_meritos_listado::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Miembros_meritos_listado::class, 'filtrar']); //filtros de datagrid
        Route::post('get_meritos', [App\Http\Controllers\Miembros_meritos_listado::class, 'get_meritos']);
    }
);
//105
Route::group(
    ['prefix' => 'mecom_obolos_reporte_talleres', 'as' => 'mecom_obolos_reporte_talleres', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_obolos_reporte_talleres::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_obolos_reporte_talleres::class, 'get_datos']);
        Route::post('set_datos', [App\Http\Controllers\Mecom_obolos_reporte_talleres::class, 'set_datos'])->middleware('escritura');
    }
);

//106
Route::group(
    ['prefix' => 'mecom_reporte_contabilidad', 'as' => 'mecom_reporte_contabilidad', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Mecom_reporte_contabilidad::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Mecom_reporte_contabilidad::class, 'get_datos']);
        Route::post('set_datos', [App\Http\Controllers\Mecom_reporte_contabilidad::class, 'set_datos']);
    }
);
//107
Route::group(
    ['prefix' => 'tramites_ini_membrecia', 'as' => 'tramites_ini_membrecia', 'middleware' => ['auth', 'permission']], function () {
        Route::any('/', [App\Http\Controllers\Tramites_ini_membrecia::class, 'index']);
        Route::post('get_datos', [App\Http\Controllers\Tramites_ini_membrecia::class, 'get_datos']);
        Route::post('filtrar', [App\Http\Controllers\Tramites_ini_membrecia::class, 'filtrar']); //filtros de datagrid
        Route::any('procesar_datos', [App\Http\Controllers\Tramites_ini_membrecia::class, 'procesar_datos']);
        Route::post('get_nombres', [App\Http\Controllers\Tramites_ini_membrecia::class, 'get_nombres']);
    }
);
