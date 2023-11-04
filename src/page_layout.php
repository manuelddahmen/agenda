<div id="errors">

</div>
<script type="text/javascript">
    function addError(var text) {
        let elementById = document.getElementById("errors");
        elementById.innerHTML += text;
    }
    function submit1(var confirm) {
        let form = document.getElementById('formSubmit');
        //form.action = 'https://google.com/search';
        //form.method = 'GET';

        form.innerHTML = form.innerHTML+'<input name="submit" value="Envoyer">';

// the form must be in the document to submit it
        //document.body.append(form);

        form.submit();
    }
</script>