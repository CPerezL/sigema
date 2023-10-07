$.fn.datebox.defaults.parser = function (s) {
  if (s) {
    var arreglo = s.split('/');
    var fecha = new Date(parseInt(arreglo[2]), parseInt(arreglo[1]) - 1, parseInt(arreglo[0]));
    return fecha;
  } else {
    return new Date();
  }
};
$.fn.datetimebox.defaults.parser = function (s) {
  if (s) {
    var arreglo = s.split('/');
    var arreglo2 = s.split(':');
    var fecha = new Date(parseInt(arreglo[2]), parseInt(arreglo[1]) - 1, parseInt(arreglo[0]), parseInt(arreglo2[0].substring(10)), parseInt(arreglo2[1]), parseInt(arreglo2[2]));
    return fecha;
  } else {
    return new Date();
  }
};

$.fn.datebox.defaults.formatter = function (date) {
  var y = date.getFullYear();
  var m = date.getMonth() + 1;
  var d = date.getDate();
  return (d < 10 ? '0' + d : d) + '/' + (m < 10 ? '0' + m : m) + '/' + y;
};

$.fn.datetimebox.defaults.formatter = function (date) {
  var dia = date.getDate();
  var y = date.getFullYear();
  var mes = date.getMonth() + 1;
  var h = date.getHours();
  var M = date.getMinutes();
  var s = date.getSeconds();
  var fecha2 = ((dia < 10) ? '0' + dia : dia) + '/' + ((mes < 10) ? '0' + mes : mes) + '/' + y + ' ' + (h < 10 ? '0' + h : h) + ':' + (M < 10 ? '0' + M : M) + ':' + (s < 10 ? '0' + s : s);
  //    var hora=( h < 10 ? '0' + h : h )+ ":" + ( M < 10 ? '0' + M : M )+ ":" +( s < 10 ? '0' + s : s );
  return (fecha2);
};

//agregar ventana
function agregarTabs(title, url, icono) {
  if ($('#tabs').tabs('exists', title)) {
    $('#tabs').tabs('select', title);
  } else {
    var content = '<iframe scrolling = "auto" frameborder = "0" src = "' + url + '?_='+Date.now()+'" style = "width: 100%; height: 100%;"> </ iframe>';
    $('#tabs').tabs('add', {
      title: title,
      border: false,
      content: content,
      closable: true,
      icon: icono
    });
  }
}

function agregarTabsold(title, url, icono) {
  if ($('#tabs').tabs('exists', title)) {
    $('#tabs').tabs('select', title);
  } else {
    $('#tabs').tabs('add', {
      title: title,
      border: false,
      href: url,
      closable: true,
      icon: icono
    });
  }
}