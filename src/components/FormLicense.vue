<template>
  <div class="k-formblock-license">
    <k-box theme="notice">
      This is an unregistered version of the kirby form block suite.
      <span v-if="doRegister" class="link" @click="$refs.regdialog.open()"
        >Register now</span
      >
    </k-box>
    <k-dialog ref="regdialog" class="k-formblock-license-dialog" size="medium">
      <k-grid gutter="medium">
        <k-column>
          <k-text size="large">
            Get your license
            <a
              href="https://license.microman.ch/?product=801346"
              target="_blank"
              >here</a
            >
          </k-text>
        </k-column>
        <k-column>
          <k-text-field
            v-model="licensekey"
            label="Please enter your license code"
            :help="supporttext()"
            required="true"
            placeholder=""
          />
        </k-column>
        <k-column>
          <k-text-field
            v-model="email"
            label="Email"
            type="text"
            required="true"
            placeholder="mail@example.com"
          />
        </k-column>
        <k-column>
          <k-box v-if="theme" class="loader-box" :theme="theme">
            <k-loader v-if="theme === 'notice'" />
            <span class="loader-text">{{ notify }}</span>
          </k-box>
        </k-column>
      </k-grid>

      <template slot="footer">
        <k-button-group>
          <k-button icon="chancel" @click="reset">Close</k-button>
          <k-button
            :disabled="onLoad || onSuccess"
            icon="check"
            theme="positive"
            @click="register"
            >Register</k-button
          >
        </k-button-group>
      </template>
    </k-dialog>
  </div>
</template>

<script>
export default {
  props: {
    message: {
      type: String,
      default() {
        return "";
      },
    },
    supportLink: {
      type: String,
      default() {
        return "";
      },
    },
    isError: Boolean,
    doRegister: {
      type: Boolean,
      default() {
        return true;
      },
    },
    doSupport: {
      type: Boolean,
      default() {
        return true;
      },
    },
  },
  data() {
    return {
      onLoad: false,
      onError: false,
      onSuccess: false,
      licensekey: "",
      email: "",
      notify: "",
    };
  },
  computed: {
    theme() {
      if (this.onError) {
        return "negative";
      }
      if (this.onSuccess) {
        return "positive";
      }
      if (this.onLoad) {
        return "notice";
      }
      return false;
    },
  },
  methods: {
    supporttext() {
      return "Keep in mind: The domain of this Kirby instance will be linked to the license. If the license is already assigned by mistake, <a href='https://microman.ch/en/microman' target='_blank'>contact the support</a>";
    },
    reset() {
      this.licensekey = this.email = this.notify = "";
      this.onError = this.onLoad = false;
      if (this.onSuccess) {
        this.$emit("onSuccess");
      }
      this.$refs.regdialog.close();
    },
    async register() {
      this.onError = this.onSuccess = false;
      this.onLoad = true;

      this.notify = "Checking your license code. Please wait...";

      this.$api
        .get("formblock/license", {
          key: this.licensekey,
          email: this.email,
        })
        .then((data) => {
          this.onLoad = false;
          this.onError = data.error;
          this.onSuccess = data.success;
          this.notify = data.text;
        });
    },
  },
};
</script>

<style>
.k-formblock-license > .k-box {
  text-align: center;
}

.k-formblock-license-dialog .k-loader {
  display: inline-block;
  margin-right: 0.4em;
}

.k-formblock-license-dialog .loader-box {
  display: flex;
  justify-content: center;
  align-items: center;
}

.k-formblock-license-dialog .loader-text {
  line-height: 1.2em;
}
.k-formblock-license .link {
  text-decoration: underline;
  cursor: pointer;
}
</style>
