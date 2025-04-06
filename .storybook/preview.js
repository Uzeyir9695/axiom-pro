/** @type { import('@storybook/vue3').Preview } */
// 🔥 Mock Ziggy's `route()` function globally
window.route = (name, params) => {
    // You can return a fake URL based on route name
    return `/${name}${params ? '?' + new URLSearchParams(params).toString() : ''}`;
};

import '../resources/css/app.css';

const preview = {
    parameters: {
        controls: {
            matchers: {
                color: /(background|color)$/i,
                date: /Date$/i,
            },
        },
    },
};

export default preview;
