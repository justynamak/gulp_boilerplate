import "jquery/dist/jquery.min.js";
import $ from "jquery";

class ValidationForm {
  constructor(form) {
    this.fields = form.querySelectorAll(".form__input");
    this.elements = [
      ...form.querySelectorAll(".form__group input"),
      ...form.querySelectorAll(".form__textarea"),
    ];
    this.fieldFile = form.querySelector("#upload");
    this.fieldConfirm = form.querySelectorAll(".confirm");
    this.form = form;
    this.formData = new FormData();
    this.handleonBlur();
    this.handleonChange();
    this.handleSubmit();

    $(this.form).find(".form-success").hide();
    $(this.form).find(".form-error").hide();
  }
  setData(elem) {
    if (
      elem.type === "text" ||
      elem.type === "number" ||
      elem.type === "email" ||
      elem.type === "textarea"
    ) {
      this.formData.append(elem.name, elem.value.trim());
    } else if (elem.type === "file") {
      this.formData.append(elem.name, elem.files[0]);
    } else if (elem.type === "checkbox") {
      const checked = elem.checked ? "tak" : "nie";
      this.formData.append(elem.name, checked);
    }
  }
  addColorText(field) {
    field.classList.add("error");
  }
  addRedBorder(elem) {
    elem.classList.add("border-red");
  }
  removeRedBorder(elem) {
    elem.classList.remove("border-red");
  }
  removeColorText(field) {
    field.classList.remove("error");
  }
  showError(field, msg) {
    field.innerHTML = msg;
  }
  removeError(field) {
    field.innerHTML = "";
  }
  checkIsEmpty(elem) {
    return elem.value.length <= 0;
  }
  checkEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
  }
  checkPhoneNumber(number) {
    var re = /^\d{9}$/;
    return re.test(number);
  }
  checkFileSize(elem, errorField = "") {
    if (elem.files.length) {
      const fsize = elem.files[0].size;
      const fileSize = Math.round(fsize / 1024 / 1024);

      if (fileSize >= 10) {
        this.showError(errorField, "Waga pliku nie może przekraczać 10MB");
        elem.value = "";

        return true;
      } else {
        this.removeError(errorField);
        return false;
      }
    } else {
      this.showError(errorField, "Załącz plik");
      return true;
    }
  }

  checkFields(e) {
    e.preventDefault();

    const elements = [...this.elements].filter((elem) =>
      elem.hasAttribute("validation")
    );

    let error = false;

    elements.forEach((elem) => {
      error = this.checkTypeValidation(
        elem,
        elem.getAttribute("validation"),
        elem.getAttribute("validation_alert")
      );
    });

    if (!error) {
      this.submitButtonDisabled(true, "Proszę czekać...");

      [...this.elements].forEach((field) => this.setData(field));

      const _this = this;

      this.formData.append("formName", $(this.form).attr("id"));

      $.ajax({
        url: "./app/ajax.php",
        data: this.formData,
        type: "POST",
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (response) {
          _this.ajaxSuccess(response);
        },
        error: function (response) {
          _this.ajaxError(response);
        },
      });
    }
  }
  checkTypeValidation(elem, type, msg = "") {
    let errorField =
      type !== "checkbox"
        ? document
            .querySelector(`#${elem.id}`)
            .closest(".form__group")
            .querySelector(".error")
        : document
            .querySelector(`#${elem.id}`)
            .closest(".form__group")
            .querySelector(".error-field");

    if (type === "notEmpty") {
      if (this.checkIsEmpty(elem)) {
        this.showError(errorField, msg);
        this.addRedBorder(elem);
        return false;
      } else {
        this.removeError(errorField);
        this.removeRedBorder(elem);
        return false;
      }
    } else if (type === "email") {
      if (this.checkEmail(elem.value)) {
        this.removeError(errorField);
        this.removeRedBorder(elem);
        return false;
      } else {
        this.showError(errorField, msg);
        this.addRedBorder(elem);
        return true;
      }
    } else if (type === "phoneNumber") {
      if (this.checkPhoneNumber(elem.value)) {
        this.removeError(errorField);
        this.removeRedBorder(elem);
        return false;
      } else {
        this.showError(errorField, msg);
        this.addRedBorder(elem);
        return true;
      }
    } else if (type === "file") {
      this.checkFileSize(elem, errorField);
    } else if (type === "checkbox") {
      if (elem.checked) {
        this.removeColorText(errorField);
        return false;
      } else {
        this.addColorText(errorField);
        return true;
      }
    }
  }
  checkIsRequired(e) {
    const required = e.currentTarget.hasAttribute("validation");
    if (required) {
      const type = e.currentTarget.getAttribute("validation");
      const msg = e.currentTarget.getAttribute("validation_alert");
      const elem = e.currentTarget;
      this.checkTypeValidation(elem, type, msg);
    }
  }

  handleonBlur() {
    this.fields.forEach((field) => {
      field.addEventListener("blur", (e) => this.checkIsRequired(e));
    });
  }
  handleonChange() {
    this.fieldFile
      ? this.fieldFile.addEventListener("change", (e) =>
          this.checkIsRequired(e)
        )
      : null;
    this.fieldConfirm
      ? this.fieldConfirm.forEach((item) =>
          item.addEventListener("change", (e) => this.checkIsRequired(e))
        )
      : null;
  }
  handleSubmit() {
    this.form.addEventListener("submit", this.checkFields.bind(this));
  }
  ajaxSuccess(response) {
    this.submitButtonDisabled(false);
    $(this.form).find(".form-success").hide();
    $(this.form).find(".form-error").hide();
    this.clearForm();

    let error = true;
    let message = "";
    let errors = [];

    if (response.error !== undefined) {
      error = response.error;
    }

    if (error) {
      $(this.form).find(".form-error").show();
    } else {
      $(this.form).find(".form-success").show();
    }
  }
  ajaxError(response) {
    this.submitButtonDisabled(false);
    $(this.form).find(".form-success").hide();
    $(this.form).find(".form-error").show();
    this.clearForm();
  }
  submitButtonDisabled(state) {
    const submit = $(this.form).find("button").eq(0);

    if (submit.length !== 1) {
      return;
    }

    if (state) {
      submit.attr("disabled", true);
    } else {
      submit.removeAttr("disabled");
    }
  }
  clearForm() {
    for (var key of this.formData.entries()) {
      const input = $('[name="' + key[0] + '"]');

      if (input.length !== 1) {
        continue;
      }

      if (
        input.is('[type="text"]') ||
        input.is("textarea") ||
        input.is('[type="file"]') ||
        input.is('[type="email"]') ||
        input.is('[type="number"]')
      ) {
        input.val("");
      } else if (input.is('[type="checkbox"]')) {
        input.prop("checked", false);
      }
    }
  }
}

export default ValidationForm;
