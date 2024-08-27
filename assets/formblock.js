// Export the "multiply" function:
export function FormBlock(config, formElement) {

    let $this = this;
    this.config = config;
    this.formElement = formElement;
    this.state = "new";
    this.data = {};

    //this.formElement = document.getElementById(this.config.form_id)

    this.submitInput = this.formElement.querySelector('[data-form="submit"]');
    this.submitBar = this.formElement.querySelector('[data-form="bar"]');

    this.onsubmit = (e) => {

        $this.data = JSON.parse(e.target.response);

        if (e.target.status === 200) {

            $this.state = $this.data.state ?? $this.data.status;

            $this.formElement.dataset.process = $this.state;
            $this.submitBar.style.width = 0;
            $this.submitInput.value = $this.config.messages.send;

            switch ($this.state) {
                case "invalid":
                    $this.onfielderror($this.data);
                    break;

                case "fatal":
                    $this.onerror($this.data.error_message);
                    break;

                case "success":
                    $this.onsuccess($this.data.success_message);
                    break;

                default:
                    break;
            }

        } else {

            //Show error in Console
            console.error( $this.data   );

            //Show Error message in Form
            $this.onerror($this.config.messages.fatal);

        }

    }

    this.onvalidate = function(e) {

        $this.data = JSON.parse(e.target.response);
        $this.state = $this.data.state;
        $this.onfieldvalidate($this.data.fields, false);

    }

    this.onfielderror = (data) => {

        $this.formElement.querySelector('[data-form="form_error"]').outerHTML = $this.data.error_message;
        $this.onfieldvalidate($this.data.fields, true);

    }

    this.onfieldvalidate = (field_data) => {

        $this.formElement.querySelector('[data-form="form_error"]').outerHTML = $this.data.error_message;

        field_data.forEach((field) => {

            let fieldElement = $this.formElement.querySelector('[data-id="' + field.slug + '"]');

            if (fieldElement !== null) {
                fieldElement.dataset.valid = field.is_valid;
                fieldElement.querySelector('[aria-describedby]')?.toggleAttribute('invalid', !field.is_valid);
                
                let errorfield = fieldElement.querySelector('[data-form="fields_error"]');
                if (errorfield) {
                    errorfield.outerHTML = field.message;
                }
            }

        });


    }

    this.onerror = (msg) => {

        $this.formElement.innerHTML = msg;
        $this.centerform();

    }

    this.onsuccess = (msg) => {

        if ($this.data.redirect != "") {
            window.location.href = $this.data.redirect;
        } else {
            $this.formElement.innerHTML = msg;
            $this.centerform();
        }


    }

    this.centerform = () => {

        $this.formElement.scrollIntoView({
            behavior: 'auto',
            block: 'center',
            inline: 'center'
        });

    }

    this.onprogress = (event) => {

        let percent = parseInt((event.loaded / event.total) * 100) + "%";

        $this.submitBar.style.width = percent;
        $this.submitInput.value = $this.config.messages.loading.replace("{{percent}}", percent);

    }

    this.validate = (field_name) => {

        $this.formdata = new FormData($this.formElement);
        $this.formdata.append("page", config.page_id);
        $this.formdata.append("lang", config.language);
        $this.formdata.append("field_validation", field_name);

        $this.formElement.querySelectorAll('[data-form="files"]').forEach(function(field) {
            if (field.dataset.form == "files") {
                $this.formdata.delete(field.name)
            }
        })

        const xhr_validate = new XMLHttpRequest();

        xhr_validate.open("POST", $this.config.endpoint, true);
        xhr_validate.addEventListener('load', $this.onvalidate);
        xhr_validate.send($this.formdata);

    }

    this.submit = (e) => {

        e.preventDefault()

        if ($this.formElement.dataset.process != "loading") {

            $this.formElement.dataset.process = "loading";

            $this.formdata = new FormData($this.formElement);
            $this.formdata.append("page", config.page_id);
            $this.formdata.append("lang", config.language);

            const xhr_submit = new XMLHttpRequest();

            xhr_submit.open("POST", $this.config.endpoint, true);

            xhr_submit.addEventListener('load', $this.onsubmit);
            xhr_submit.addEventListener('error', $this.onerror);
            xhr_submit.upload.addEventListener('progress', $this.onprogress);

            xhr_submit.send($this.formdata);

        }

    }

    this.formElement.querySelectorAll("[data-form='field'").forEach(function(el) {

        el.addEventListener("change", function(e) {
            $this.validate(e.target.closest("[data-id]").dataset.id)
        });

    });

    this.formElement.addEventListener("submit", this.submit);
};