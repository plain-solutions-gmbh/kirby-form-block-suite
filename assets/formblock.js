// Export the "multiply" function:
export function FormBlock(config) {

    let $this = this;
    this.config = config;
    this.state = "new";
    this.data = {};

    this.form_element = document.getElementById(this.config.form_id)

    this.submit_input = this.form_element.querySelector('[data-form="submit"]');
    this.submit_bar = this.form_element.querySelector('[data-form="bar"]');

    this.onsubmit = (e) => {

        $this.data = JSON.parse(e.target.response);

        if (e.target.status === 200) {

            $this.state = $this.data.state ?? $this.data.status;

            $this.form_element.dataset.process = $this.state;
            $this.submit_bar.style.width = 0;
            $this.submit_input.value = $this.config.messages.send;

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

        $this.form_element.querySelector('[data-form="form_error"]').outerHTML = $this.data.error_message;
        $this.onfieldvalidate($this.data.fields, true);

    }

    this.onfieldvalidate = (field_data, onsubmit) => {

        $this.form_element.querySelector('[data-form="form_error"]').outerHTML = $this.data.error_message;

        field_data.forEach((field) => {

            let field_element = $this.form_element.querySelector('[data-id="' + field.slug + '"]');

            if (field_element !== null) {
                field_element.dataset.valid = field.is_valid;
                field_element.querySelector('[aria-describedby]').toggleAttribute('invalid', !field.is_valid);
                field_element.querySelector('[data-form="fields_error"]').outerHTML = field.message;
            }

        });


    }

    this.onerror = (msg) => {

        $this.form_element.innerHTML = msg;
        $this.centerform();

    }

    this.onsuccess = (msg) => {

        if ($this.data.redirect != "") {
            window.location.href = $this.data.redirect;
        } else {
            $this.form_element.innerHTML = msg;
            $this.centerform();
        }


    }

    this.centerform = () => {

        $this.form_element.scrollIntoView({
            behavior: 'auto',
            block: 'center',
            inline: 'center'
        });

    }

    this.onprogress = (event) => {

        let percent = parseInt((event.loaded / event.total) * 100) + "%";

        $this.submit_bar.style.width = percent;
        $this.submit_input.value = $this.config.messages.loading.replace("{{percent}}", percent);

    }

    this.validate = (field_name) => {

        $this.formdata = new FormData($this.form_element);
        $this.formdata.append("page", config.page_id);
        $this.formdata.append("lang", config.language);
        $this.formdata.append("field_validation", field_name);

        $this.form_element.querySelectorAll('[data-form="files"]').forEach(function(field) {
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

        if ($this.form_element.dataset.process != "loading") {

            $this.form_element.dataset.process = "loading";

            $this.formdata = new FormData($this.form_element);
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

    this.form_element.querySelectorAll("[data-form='field'").forEach(function(el) {

        el.addEventListener("change", function(e) {
            $this.validate(e.target.closest("[data-id]").dataset.id)
        });

    });

    this.form_element.addEventListener("submit", this.submit);
};