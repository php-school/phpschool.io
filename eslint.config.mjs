import js from "@eslint/js";
import pluginVue from "eslint-plugin-vue";
import skipFormatting from "@vue/eslint-config-prettier/skip-formatting";
import globals from "globals";

export default [
  { ignores: ["vendor/**", "public/**", "var/**", "cache/**", "assets/.vite-ssg-temp/**"] },
  js.configs.recommended,
  ...pluginVue.configs["flat/essential"],
  skipFormatting,
  {
    languageOptions: {
      ecmaVersion: "latest",
      globals: { ...globals.browser, ...globals.node },
    },
  },
];
