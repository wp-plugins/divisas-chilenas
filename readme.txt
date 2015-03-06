=== Divisas Chilenas ===
Contributors: gersonapablaza
Donate link: Pendiente
Tags: divisas, chile, indicadores, econom&iacute;a, UF, d&oacute;lar, UTM, indicadores econ&oacute;micos
Requires at least: 4.1.0
Tested up to: 4.1.1
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Despliega los valores de las principales divisas de Chile para la fecha actual obtenidos utilizando la API provista por mindicador.cl

== Description ==

Este plugin genera un widget donde se despliegan las principales divisas para Chile en el d&iacute;a actual. Para &eacute;sto las divisas son consultadas a la API Rest provista por mindicador.cl de manera gratuita.

Algunas de las caracter&iacute;sticas de este plugin son:

* Se consulta lo menos posible el servicio externo http://mindicador.cl/api.
* Permite seleccionar que indicadores se desean desplegar.
* Permite agregar una clase &ldquo;css&quot; al widget de manera de poder personalizar su dise&ntilde;o en cada una de sus instancias.

== Installation ==

1. Descomprime el archivo .zip
1. Sube la carpeta obtenida al directorio '/wp-content/plugins/'
1. Activa el plugin a trav&eacute;s del men&uacute; 'Plugins' en el panel de administraci&oacute;n de WordPress
1. En el men&uacute; 'widgets' arrastra el widget 'Divisas Chilenas' a la secci&oacute;n de la p&aacute;gina que se desee

== Frequently Asked Questions ==

= Qu&eacute; indicadores se pueden desplegar? =

Actualmente la UF, el d&oacute;lar y la UTM.

= Hay un indicador que no despliega su valor =

&Eacute;sto puede ser porque cuando se consulta al API para obtener los valores no estaba disponible el de ese indicador, por lo que por el resto del d&iacute;a no se volver&aacute; a obtener.
Una posible soluci&oacute;n es desactivar y volver a activar el plugin.

= Se puede modificar el estilo visual? = 

Si se puede, incluso para cada instancia del plugin, ya que se puede agregar una clase css personalizada a la cual escribirle sus respectivas reglas visuales ya que cada componente tiene disponible un selector.

== Screenshots ==

1. Par&aacute;metros del widget
2. Despliegue por defecto
3. Ejemplo de un despliegue personalizado

== Changelog ==

= 1.0.0 =
* Versi&oacute;n inicial.

== ToDo List ==

* Que se puede agregar como shortcode.
* Para el caso del d&oacute;lar los fines de semana, devolver el &uacute;ltimo valor observado.
* Mejorar la l&oacute;gica para que pueda actualizar el valor durante el d&iacute;a bajo alg&uacute;n desencadenante por definir.

