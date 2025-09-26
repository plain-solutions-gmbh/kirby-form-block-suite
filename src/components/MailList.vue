<!-- eslint-disable vue/no-v-html -->
<template>
  <div class="k-mailview-list">
    <div v-if="!hideheader" class="k-mailview-list-header">
      <k-box
        :theme="value.header.state.theme"
        :icon="isOpen ? 'angle-up' : 'angle-down'"
        :text="headerText"
        @click.native="toggleOpen()"
      />
      <k-button
        icon="download"
        variant="filled"
        :link="value.header.download"
        :theme="value.header.state.theme"
        :download="true"
      ></k-button>
    </div>

    <k-button
      v-else
      icon="download"
      class="k-mailview-export"
      :link="value.header.download"
      :text="this.$t('form.block.inbox.export')"
      :download="true"
    ></k-button>

    <k-items v-if="isOpen || hideheader" :items="items" />
  </div>
</template>

<script>
export default {
  props: {
    value: {
      type: Array,
      required: true,
    },
    showuuid: Boolean,
    hideheader: Boolean,
  },
  data() {
    return {
      isOpen: false,
    };
  },
  computed: {
    items() {
      const a = this.value.content;

      if (a.length === 0) {
        return [
          {
            text: this.$t("form.block.inbox.empty"),
            theme: "disabled",
          },
        ];
      }

      return this.value.content;
    },
    headerText() {
      if (this.showuuid) {
        return this.value.header.name + " (" + this.value.uuid + ")";
      }
      return this.value.header.name;
    },
  },
  created() {
    this.isOpen =
      sessionStorage.getItem(
        `plain.form.showOpen.${this.value.page}.${this.value.uuid}`
      ) === "on";
  },
  methods: {
    toggleOpen() {
      this.isOpen = !this.isOpen;
      sessionStorage.setItem(
        `plain.form.showOpen.${this.value.page}.${this.value.uuid}`,
        this.isOpen ? "on" : "off"
      );
    },
  },
};
</script>

<style>
.k-mailview-list {
  background: none;
  box-shadow: none;
  border-radius: 0;
  
}
.k-mailview-export {
  display: flex;
  justify-content: end;
}
.k-mailview-list-header {
  position: relative;
}

.k-mailview-list-header > .k-button {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  right: var(--spacing-1);
}
</style>
