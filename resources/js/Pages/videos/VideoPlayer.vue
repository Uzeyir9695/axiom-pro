<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import vue3StarRatings from "vue3-star-ratings";
import videojs from 'video.js';
import 'video.js/dist/video-js.css';

const props = defineProps({
    videoUrl: {
        type: String,
        default: 'sample_video.mp4',
    },
    controlColor: {
        type: String,
        default: '#ee0f0f',
    },
    popupEnabled: {
        type: Boolean,
        default: true,
    },
    popupContent: {
        type: String,
        default: 'How would you rate our lesson out of 5?',
    },
});

const rate = ref(0);
const videoRef = ref(null);
let player = null;
const showPopup = ref(false);
const popupTriggered = ref(false);

onMounted(() => {
    if (!videoRef.value) return;

    player = videojs(videoRef.value, {
        controls: true,
        autoplay: false,
        preload: 'auto',
        sources: [{
            src: '/video_lessons/'+props.videoUrl,
            type: 'video/mp4',
        }],
    });

    player.on('loadedmetadata', () => {
        player.on('timeupdate', () => {
            const timeLeft = player.duration() - player.currentTime();

            // If popupEnabled is true and timeLeft <= 10 seconds, show popup
            if (timeLeft <= 10 && props.popupEnabled) {
                showPopup.value = true;
                popupTriggered.value = true;
            }
        });

        player.on('ended', () => {
            showPopup.value = false;
            popupTriggered.value = false;
        });
    });

    onBeforeUnmount(() => {
        if (player) {
            player.dispose();
        }
    });
});
</script>

<template>
    <div class="relative" :style="{ '--control-color': props.controlColor }">
        <video ref="videoRef" class="video-js vjs-4-3">
            Your browser does not support the video tag.
        </video>

        <div v-if="showPopup" class="absolute top-0 right-0 h-auto w-fit p-3 rounded-lg bg-black bg-opacity-70 flex flex-col items-center justify-center text-white text-lg font-bold z-50">
            {{ popupContent }}
            <vue3-star-ratings v-model="rate" inactiveColor="#64748B"/>
        </div>
    </div>

</template>

<style>
.video-js .vjs-play-progress {
    background-color: var(--control-color) !important;
}

.video-js .vjs-big-play-button {
    background-color: var(--control-color) !important;
    border-radius: 50%;
}

.video-js .vjs-volume-level {
    background-color: var(--control-color) !important;
}
</style>
