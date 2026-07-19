import Form from "./components/blocks/Form.vue";
import MailDialog from "./components/dialog/Form.vue";
import MailList from "./components/MailList.vue";
import MailView from "./components/fields/MailView.vue";

window.panel.plugin("plain/formblock", {
  fields: {
    mailview: MailView,
  },
  components: {
    "k-mail-list": MailList,
    "k-mail-dialog": MailDialog,
  },
  blocks: {
    form: Form,
  },
});
