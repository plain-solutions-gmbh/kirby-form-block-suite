<template>
  <k-box v-if="msg.length > 0" :theme="state" class="k-formblock-license">
    {{ $t(msg) }}
    <span v-if="state === 'notice'" href="#" @click="dialog()">{{
      $t("form.block.license.info.link")
    }}</span>
  </k-box>
</template>

<script>
export default {
  props: {
    text: {
      type: String,
      default() {
        return "";
      },
    },
  },
  data() {
    return {
      state: "notice",
      msg: this.text,
    };
  },
  methods: {
    dialog() {
      const $this = this;
      this.$dialog("formblock/register", {
        on: {
          success(t) {
            $this.msg = t.message;
            $this.state = "positive";
            $this.$panel.dialog.close();
          },
        },
      });
    },
  },
};
</script>

<style>
.k-formblock-license > span {
  text-decoration: underline;
  cursor: pointer;
}
</style>
