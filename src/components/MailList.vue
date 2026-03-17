<!-- eslint-disable vue/no-v-html -->
<template>
  <div class="k-mailview-list">
    <div v-if="hideheader" class="k-mailview-toolbar">
      <div class="k-mailview-storage-toggle">
        <label class="k-mailview-storage-label">{{ $t('form.block.inbox.storage') }}</label>
        <k-toggle-input :value="storageEnabled" @input="toggleStorage" />
      </div>
      <k-button-group v-if="value.content.length > 0" class="k-mailview-buttons">
        <k-button icon="cancel" theme="red" :text="$t('delete.all')" @click="onDelete"></k-button>
        <k-button icon="download" :link="value.header.download" :text="$t('form.block.inbox.export')"
          :download="true"></k-button>
      </k-button-group>
    </div>

    <div v-if="!hideheader" class="k-mailview-list-header">
      <k-box :theme="value.header.state.theme" :icon="isOpen ? 'angle-up' : 'angle-down'" :text="headerText"
        @click.native="toggleOpen()" />
      <k-button icon="download" variant="filled" :link="value.header.download" :theme="value.header.state.theme"
        :download="true"></k-button>
    </div>

    <template v-if="isOpen || hideheader">
      <k-box v-if="value.content.length === 0" theme="info" :text="$t('form.block.inbox.empty')" />
      <k-items v-else :items="items" />
    </template>
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
      storageEnabled: true,
    };
  },
  computed: {
    items() {
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
    this.storageEnabled = this.value.storageEnabled !== false;
  },
  methods: {
    toggleOpen() {
      this.isOpen = !this.isOpen;
      sessionStorage.setItem(
        `plain.form.showOpen.${this.value.page}.${this.value.uuid}`,
        this.isOpen ? "on" : "off"
      );
    },
    toggleStorage(newState) {
      this.$api
        .get("formblock", {
          action: 'setStorageEnabled',
          page_id: this.value.page,
          form_id: this.value.id,
          params: JSON.stringify({ enabled: newState })
        })
        .then(() => {
          this.storageEnabled = newState;
        });
    },
    onDelete() {
      this.$panel.dialog.open({
        component: 'k-remove-dialog',
        props: {
          text: this.$t('field.entries.delete.confirm.all'),
        },
        on: {
          submit: () => {
            this.$api
              .get("formblock", {
                action: 'deleteAll',
                page_id: this.value.page,
                form_id: this.value.id
              })
              .then((data) => {
                this.$emit('refresh')
                this.$panel.dialog.close()
              });
          },
        },

      })
    }
  },
};
</script>

<style>
.k-mailview-list {
  background: none;
  box-shadow: none;
  border-radius: 0;

}

.k-mailview-toolbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: var(--spacing-3);
}

.k-mailview-buttons {
  display: flex;
  align-items: center;
}

.k-mailview-storage-toggle {
  display: flex;
  align-items: center;
  gap: var(--spacing-2);
}

.k-mailview-storage-label {
  font-size: var(--text-sm);
  white-space: nowrap;
}

.k-mailview-list-header {
  position: relative;
}

.k-mailview-list-header>.k-button {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  right: var(--spacing-1);
}
</style>
