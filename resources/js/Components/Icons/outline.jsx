// Extra icons

import { defineComponent } from 'vue'

export const EmptyCircleIcon = defineComponent({
    setup() {
        return () => (
            <svg
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M20.3149 15.4442C20.7672 14.3522 21 13.1819 21 12C21 9.61305 20.0518 7.32387 18.364 5.63604C16.6761 3.94821 14.3869 3 12 3C9.61305 3 7.32387 3.94821 5.63604 5.63604C3.94821 7.32387 3 9.61305 3 12C3 13.1819 3.23279 14.3522 3.68508 15.4442C4.13738 16.5361 4.80031 17.5282 5.63604 18.364C6.47177 19.1997 7.46392 19.8626 8.55585 20.3149C9.64778 20.7672 10.8181 21 12 21C13.1819 21 14.3522 20.7672 15.4442 20.3149C16.5361 19.8626 17.5282 19.1997 18.364 18.364C19.1997 17.5282 19.8626 16.5361 20.3149 15.4442Z"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        )
    },
})

export const Copy03Icon = defineComponent({
    setup() {
        return () => (
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_2519_21928)">
                    <path d="M5.33398 5.33398V3.46732C5.33398 2.72058 5.33398 2.34721 5.47931 2.062C5.60714 1.81111 5.81111 1.60714 6.062 1.47931C6.34721 1.33398 6.72058 1.33398 7.46732 1.33398H12.534C13.2807 1.33398 13.6541 1.33398 13.9393 1.47931C14.1902 1.60714 14.3942 1.81111 14.522 2.062C14.6673 2.34721 14.6673 2.72058 14.6673 3.46732V8.53398C14.6673 9.28072 14.6673 9.65409 14.522 9.93931C14.3942 10.1902 14.1902 10.3942 13.9393 10.522C13.6541 10.6673 13.2807 10.6673 12.534 10.6673H10.6673M3.46732 14.6673H8.53398C9.28072 14.6673 9.65409 14.6673 9.93931 14.522C10.1902 14.3942 10.3942 14.1902 10.522 13.9393C10.6673 13.6541 10.6673 13.2807 10.6673 12.534V7.46732C10.6673 6.72058 10.6673 6.34721 10.522 6.062C10.3942 5.81111 10.1902 5.60714 9.93931 5.47931C9.65409 5.33398 9.28072 5.33398 8.53398 5.33398H3.46732C2.72058 5.33398 2.34721 5.33398 2.062 5.47931C1.81111 5.60714 1.60714 5.81111 1.47931 6.062C1.33398 6.34721 1.33398 6.72058 1.33398 7.46732V12.534C1.33398 13.2807 1.33398 13.6541 1.47931 13.9393C1.60714 14.1902 1.81111 14.3942 2.062 14.522C2.34721 14.6673 2.72058 14.6673 3.46732 14.6673Z" stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                <defs>
                    <clipPath id="clip0_2519_21928">
                        <rect width="16" height="16" fill="white"/>
                    </clipPath>
                </defs>
            </svg>

        )
    },
})

export const Icon = defineComponent({
    setup() {
        return () => (
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10.2051 13.4102V9.72774M10.2042 7.08374H10.2125M10.2083 2.5C14.465 2.5 17.9167 5.95083 17.9167 10.2083C17.9167 14.465 14.465 17.9167 10.2083 17.9167C5.95083 17.9167 2.5 14.465 2.5 10.2083C2.5 5.95083 5.95083 2.5 10.2083 2.5Z" stroke="#363636" stroke-width="1.25" stroke-linecap="square" stroke-linejoin="round"/>
            </svg>
        )
    },
})

export const UploadIcon = defineComponent({
    setup() {
        return () => (
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9.99912 2.69106V13.3392M7.27026 4.80771L9.9994 2.06641L12.7295 4.80771M13.8535 9.16074H17.7077V17.9307H2.29102V9.16074H6.14518" stroke="currentColor" stroke-width="1.25" stroke-linecap="square"/>
            </svg>
        )
    },
})

export const XIcon = defineComponent({
    setup() {
        return () => (
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17 7L7 17M7 7L17 17" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                      stroke-linejoin="round"/>
            </svg>
        )
    },
})
