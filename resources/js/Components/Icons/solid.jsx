// Extra icons

import { defineComponent } from 'vue'

export const EmptyCircleIcon = defineComponent({
    setup() {
        return () => (
            <svg
                viewBox="0 0 20 20"
                fill="currentColor"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    fill-rule="evenodd"
                    clip-rule="evenodd"
                    d="M10 18C12.1217 18 14.1566 17.1571 15.6569 15.6569C17.1571 14.1566 18 12.1217 18 10C18 7.87827 17.1571 5.84344 15.6569 4.34315C14.1566 2.84285 12.1217 2 10 2C7.87827 2 5.84344 2.84285 4.34315 4.34315C2.84285 5.84344 2 7.87827 2 10C2 12.1217 2.84285 14.1566 4.34315 15.6569C5.84344 17.1571 7.87827 18 10 18Z"
                />
            </svg>
        )
    },
})

export const SuccessIcon = defineComponent({
    setup() {
        return () => (
            <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="24" cy="24" r="24" fill="url(#paint0_linear_2519_23482)"/>
                <path d="M24 0C10.7676 0 0 10.7664 0 23.9972C0 37.2281 10.7676 48 24 48C37.2324 48 48 37.2336 48 24.0028C48 10.7719 37.2324 0 24 0ZM34.3411 19.4226L22.3383 32.3434C21.7955 32.9304 21.0533 33.2295 20.3056 33.2295C19.663 33.2295 19.0261 33.008 18.4999 32.5649L12.036 27.0266C10.8728 26.0298 10.7399 24.2852 11.7369 23.1222C12.7339 21.9592 14.4787 21.8207 15.6418 22.8231L20.084 26.6279L30.2756 15.6566C31.3169 14.5379 33.0672 14.4714 34.1916 15.5126C35.3159 16.5538 35.3769 18.3039 34.3411 19.4226Z" fill="#2AC500"/>
                <defs>
                    <linearGradient id="paint0_linear_2519_23482" x1="44.5968" y1="8.66785" x2="-15.2182" y2="0.397062" gradientUnits="userSpaceOnUse">
                        <stop stop-color="white"/>
                        <stop offset="1" stop-color="white" stop-opacity="0.61"/>
                    </linearGradient>
                </defs>
            </svg>

        )
    },
})