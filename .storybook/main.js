

/** @type { import('@storybook/vue3-vite').StorybookConfig } */
const config = {
  "stories": [
      "../resources/js/**/*.stories.@(js|jsx|ts|tsx)",
  ],
  "addons": [
    "@storybook/addon-essentials",
    "@storybook/addon-onboarding",
    "@chromatic-com/storybook",
    "@storybook/experimental-addon-test",
  ],
  "framework": {
    "name": "@storybook/vue3-vite",
    "options": {}
  },
    staticDirs: ['../public'],
};
export default config;
