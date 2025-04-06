<script setup>
import {Deferred, Link} from "@inertiajs/vue3";
import VideoPlayer from "./VideoPlayer.vue";

defineProps({
  videos: {
    type: Array,
  }
})

</script>

<template>
  <Deferred data="videos">
    <div class="container mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 my-10">
      <div v-for="(video, index) in videos" :key="video.title+'_'+index" class="bg-slate-600 rounded-xl shadow-md overflow-hidden">
        <Link :href="route('videos.show', video.id)">
            <VideoPlayer :video-url="video.video_url" />
        </Link>
        <div class="p-4">
          <Link :href="route('videos.show', video.id)" class="text-lg font-semibold text-slate-300 line-clamp-1">
            {{ video.title }}
          </Link>
          <p class="mt-2 text-slate-300 text-sm line-clamp-3">
            {{ video.description }}
          </p>
            <span class="text-sm font-bold">Total Reviews: {{ video.rates_sum_rate > 0 ? video.rates_sum_rate : 0 }}</span>
        </div>
      </div>
    </div>
    <template #fallback>
      <div class="text-center text-gray-500 py-10">Videos Not Found!</div>
    </template>
  </Deferred>
</template>

<style scoped>

</style>
