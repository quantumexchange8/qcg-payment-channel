<script setup>
import { DotLottieVue } from '@lottiefiles/dotlottie-vue'
import GuestLayout from "@/Layouts/GuestLayout.vue";
</script>

<template>
    <GuestLayout>
        <div class="flex flex-col gap-2 items-center">
            <span class="text-xl font-semibold">Dot Lottie</span>
            <DotLottieVue style="height: 500px; width: 500px" autoplay loop src="../lottieAnimations/candidate-cv.lottie" />
        </div>
        <div class="flex flex-col gap-2 items-center">
            <span class="text-xl font-semibold">Lottie Json</span>
            <DotLottieVue style="height: 500px; width: 500px" autoplay loop src="../lottieAnimations/candidate-cv.json" />
        </div>
    </GuestLayout>
</template>
