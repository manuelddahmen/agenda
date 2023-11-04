<?php //printJsPhpErrors();
?>
<table class="footer signature">
    <tr>
        <td><p>Signature coordinateur</p><p></p></td>
        <td><p>Signature patient</p><p></p><?php
            global $date;
            echo date_locale_fr($date);
            ?></td>
    </tr>
</table>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-XDYCBMNZ13"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-XDYCBMNZ13');
</script>
