<template>

  <div class="k-block-type-form">

    <div class="k-block-type-form-wrapper" :data-state="state"  @click="open">

      <k-input v-model="content.name" name="name" type="text" @input="onInput"/>
      <k-tag :data-state="status.state">{{$t('form.block.inbox.show')}} ({{status.text}})</k-tag>

    </div>

  </div>
</template>

<script>

export default {
  data () {
    return {
      status: {
        type: Object,
        default: {
          count: "-",
          read: "-",
          fail: "-",
          state: "wait"
        }
      }
    }
  },
  computed: {
    thisPage () {
      return this.$attrs.endpoints.model.replace('/pages/', '').replace(/\+/g, '/')
    }
  },
  destroyed() {
 
    this.$events.$off("form.update", this.updateCount);

  },
  created () {

      const $this = this;
      this.$store.subscribe(function(mutation) {
        if (mutation.type =="content/STATUS") 
          $this.$events.$emit("form.update");
      })

    this.updateCount()
    
    this.$events.$on("form.update", this.updateCount);

  },
  methods: {

    updateCount() {

      const $this = this;
      this.$api.get("form/get-requests-count", {form: (this.thisPage + "/" + this.$attrs.id), name: this.content.name})
        .then( (data) => $this.status = data,)
        .catch(function () {
          $this.error = $this.$t('form.block.inbox.error');
        }
      )

    },

		confirmToRemove() {
      
			this.$refs.removeDialog.open();

		},
    onInput (value) {

      this.$emit("update", value);

    }
  },
  
};
</script>


<style lang="scss">

  .k-block-type-form {

    .k-block-type-form-wrapper, .k-input, .k-tag {
      display: inline-flex;
      width: auto;
    }

    .k-tag {

      background-color: var(--color-gray-300);

        &[data-state=new] {
            color:var(--color-white);
            background-color: var(--color-green);
        }
        &[data-state=ok] {
            color: var(--color-gray-600);
        }
        &[data-state=error] {
            background-color: var(--color-red);
            color:var(--color-white);
        }
    }

    .k-block-type-form-wrapper {
      border: 1px solid var(--color-gray-300);
    }


    .k-text-input {
      padding-left:  0.3rem;
    }
    
  }

</style>