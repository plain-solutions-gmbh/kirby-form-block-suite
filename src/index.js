import Form from "./components/blocks/Form.vue";
import MailDialog from "./components/dialog/Form.vue";
import MailList from "./components/MailList.vue";
import MailView from "./components/fields/MailView.vue";
import FormLicense from "./components/FormLicense.vue";

window.panel.plugin("microman/formblock", {
  fields: {
    mailview: MailView,
  },
  components: {
    "k-mail-list": MailList,
    "k-mail-dialog": MailDialog,
    "k-formblock-license": FormLicense,
  },
  blocks: {
    form: Form,
  },
});
