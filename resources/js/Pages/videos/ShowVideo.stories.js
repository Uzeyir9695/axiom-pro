import Show from './Show.vue';
import VideoPlayer from './VideoPlayer.vue';
import Layout from '../../Shared/layouts/Layout.vue';

export default {
    title: 'Crypto School Assessment/VideoPlayer',
    component: Show,
    subcomponents: { VideoPlayer },
    tags: ['autodocs'],
    args: {
        video: {
            video_url: 'sample_video.mp4',
            title: 'Sample Video Title',
            description: 'This is a description of the video lesson.',
        },
        name: 'Crypto School',
        showLogo: true,
        moveLogoToRight: false,
        popupEnabled: true,
        popupContent: 'How would you rate our lesson out of 5?',
        controlColor: '#ee0f0f',
    },
    argTypes: {
        'name': { control: 'text' },
        'showLogo': { control: 'boolean' },
        'moveLogoToRight': { control: 'boolean' },
        'popupEnabled': { control: 'boolean' },
        'popupContent': { control: 'text' },
        'controlColor': { control: 'color' },
    },

};

export const Video = {
    render: (args) => ({
        components: { Show, VideoPlayer, Layout },
        setup() {
            return { args };
        },
        template: `
            <Layout>
            <Show :video="args.video"
                  :name="args.name"
                  :show-logo="args.showLogo"
                  :move-logo-to-right="args.moveLogoToRight"
            >
                <template #video-player>
                    <VideoPlayer
                        :popup-enabled="args.popupEnabled"
                        :popup-content="args.popupContent"
                        :control-color="args.controlColor"
                    />
                </template>
            </Show>
            </Layout>
        `
    }),
};

