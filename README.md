
<h1>ANALISIS ZAP PREVIO</h1>
[2026-08-17-ZAP-Report-PREVIO.html](https://github.com/user-attachments/files/31164897/2026-08-17-ZAP-Report-PREVIO.html)
<h1>ANALISIS ZAP CORREGIDO</h1>
[2026-08-17-ZAP-Report-CORREGIDO.html](https://github.com/user-attachments/files/31164898/2026-08-17-ZAP-Report-CORREGIDO.html)
<h1>TABLA COMPARATIVA</h1>
[Correciones ZAP.pdf](https://github.com/user-attachments/files/31164964/Correciones.ZAP.pdf)

<h2 align="center">📊 Tabla comparativa de resultados OWASP ZAP</h2>

<p align="center">
  <strong>Antes vs. Después de aplicar correcciones de seguridad</strong>
</p>

<table>
  <thead>
    <tr>
      <th>Alerta encontrada</th>
      <th>Riesgo</th>
      <th>Corrección realizada</th>
      <th>Resultado final</th>
    </tr>
  </thead>
  <tbody>
    <!-- Alertas originales -->
    <tr>
      <td><strong>Cabecera Content Security Policy (CSP) no configurada</strong></td>
      <td><span style="color:#ff9800;">Medio</span></td>
      <td>Se agregó la cabecera Content-Security-Policy con política restrictiva (default-src 'self') en .htaccess y PHP.</td>
      <td><span style="color:#4caf50; font-weight:bold;">✔ Corregida</span></td>
    </tr>
    <tr>
      <td><strong>Falta atributo de integridad de recursos secundarios</strong></td>
      <td><span style="color:#ff9800;">Medio</span></td>
      <td>Se añadió el atributo integrity y crossorigin a todos los recursos CDN y se descargaron localmente los críticos.</td>
      <td><span style="color:#4caf50; font-weight:bold;">✔ Corregida</span></td>
    </tr>
    <tr>
      <td><strong>Falta de cabecera Anti-Clickjacking</strong></td>
      <td><span style="color:#ff9800;">Medio</span></td>
      <td>Se agregó X-Frame-Options: DENY en el servidor.</td>
      <td><span style="color:#4caf50; font-weight:bold;">✔ Corregida</span></td>
    </tr>
    <tr>
      <td><strong>Cookie Sin Flag HttpOnly</strong></td>
      <td><span style="color:#f44336;">Bajo</span></td>
      <td>Se configuró session.cookie_httponly = 1 en php.ini y se forzó en session_set_cookie_params().</td>
      <td><span style="color:#4caf50; font-weight:bold;">✔ Corregida</span></td>
    </tr>
    <tr>
      <td><strong>Cookie sin el atributo SameSite</strong></td>
      <td><span style="color:#f44336;">Bajo</span></td>
      <td>Se configuró session.cookie_samesite = Lax en php.ini y en código.</td>
      <td><span style="color:#4caf50; font-weight:bold;">✔ Corregida</span></td>
    </tr>
    <tr>
      <td><strong>El servidor divulga información mediante X-Powered-By</strong></td>
      <td><span style="color:#f44336;">Bajo</span></td>
      <td>Se deshabilitó expose_php = Off en php.ini y se eliminó la cabecera con header_remove().</td>
      <td><span style="color:#4caf50; font-weight:bold;">✔ Corregida</span></td>
    </tr>
    <tr>
      <td><strong>El servidor filtra información de versión a través del campo "Server"</strong></td>
      <td><span style="color:#f44336;">Bajo</span></td>
      <td>Se intentó ocultar con ServerTokens Prod y ServerSignature Off (no permitido en .htaccess local).</td>
      <td><span style="color:#ff9800; font-weight:bold;">⚠ No corregida (limitación de XAMPP)</span></td>
    </tr>
    <tr>
      <td><strong>Falta encabezado X-Content-Type-Options</strong></td>
      <td><span style="color:#f44336;">Bajo</span></td>
      <td>Se agregó X-Content-Type-Options: nosniff en el servidor.</td>
      <td><span style="color:#4caf50; font-weight:bold;">✔ Corregida</span></td>
    </tr>
    <tr>
      <td><strong>Fuga de información en el Banner de la Página</strong></td>
      <td><span style="color:#f44336;">Bajo</span></td>
      <td>Se deshabilitó display_errors y se implementó manejador de errores genérico.</td>
      <td><span style="color:#4caf50; font-weight:bold;">✔ Corregida</span></td>
    </tr>
    <tr>
      <td><strong>Inclusión de archivos fuente JavaScript entre dominios</strong></td>
      <td><span style="color:#f44336;">Bajo</span></td>
      <td>Se verificaron scripts externos, se añadió SRI y se descargaron localmente los no confiables.</td>
      <td><span style="color:#ff9800; font-weight:bold;">🟡 Mitigada</span></td>
    </tr>
    <tr>
      <td><strong>Atributo de elemento HTML controlable por el usuario (XSS potencial)</strong></td>
      <td><span style="color:#2196f3;">Informativo</span></td>
      <td>Se aplicó htmlspecialchars() en todas las salidas de variables de usuario.</td>
      <td><span style="color:#4caf50; font-weight:bold;">✔ Corregida</span></td>
    </tr>
    <tr>
      <td><strong>Respuesta de Gestión de Sesión Identificada</strong></td>
      <td><span style="color:#2196f3;">Informativo</span></td>
      <td>No requiere corrección; es una alerta informativa.</td>
      <td><span style="color:#9e9e9e; font-weight:bold;">📄 Documentada</span></td>
    </tr>
  </tbody>
</table>

<h3>🆕 Nuevas alertas detectadas (no estaban en el primer informe)</h3>

<table>
  <thead>
    <tr>
      <th>Nueva alerta</th>
      <th>Riesgo</th>
      <th>Motivo</th>
      <th>Estado</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td><strong>CSP: Directiva Wildcard</strong></td>
      <td><span style="color:#ff9800;">Medio</span></td>
      <td>La política CSP incluía un comodín * que anula la seguridad.</td>
      <td><span style="color:#f44336; font-weight:bold;">✖ No corregida (se eliminó el * en la corrección final)</span></td>
    </tr>
    <tr>
      <td><strong>CSP: script-src unsafe-inline</strong></td>
      <td><span style="color:#ff9800;">Medio</span></td>
      <td>Se permitía unsafe-inline en script-src, aumentando el riesgo de XSS.</td>
      <td><span style="color:#f44336; font-weight:bold;">✖ No corregida (se eliminó unsafe-inline en la corrección final)</span></td>
    </tr>
    <tr>
      <td><strong>CSP: style-src unsafe-inline</strong></td>
      <td><span style="color:#ff9800;">Medio</span></td>
      <td>Se permitía unsafe-inline en style-src, similar al anterior.</td>
      <td><span style="color:#f44336; font-weight:bold;">✖ No corregida (se eliminó unsafe-inline en la corrección final)</span></td>
    </tr>
  </tbody>
</table>

<hr>

<h3>📌 Resumen de resultados</h3>

<ul>
  <li><strong>✔ Corregidas:</strong> 9</li>
  <li><strong>🟡 Mitigadas:</strong> 1</li>
  <li><strong>⚠ No corregidas (limitación del entorno):</strong> 1 (campo "Server")</li>
  <li><strong>📄 Documentadas (Informativas):</strong> 1</li>
  <li><strong>✖ Nuevas alertas (ya corregidas en la versión final):</strong> 3</li>
</ul>

<p><em>Las nuevas alertas de CSP (wildcard y unsafe-inline) fueron eliminadas en la versión final de la política CSP, por lo que no persisten en el escaneo definitivo.</em></p>
<img width="1870" height="633" alt="image" src="https://github.com/user-attachments/assets/4ee38638-a998-4ca3-9b51-6b192bbb3f39" />

<img width="1480" height="606" alt="image" src="https://github.com/user-attachments/assets/e1454cb8-3457-4bf6-8ca1-ee17e6284ecc" />
