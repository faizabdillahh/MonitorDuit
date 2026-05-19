<template>
  <svg viewBox="0 0 240 50" fill="none" xmlns="http://www.w3.org/2000/svg" :class="classes">
    <!-- Icon Group -->
    <g transform="translate(5, 5)">
      <!-- Receipt Background with Zigzag bottom -->
      <path 
        d="M 5 5 C 5 2.2 7.2 0 10 0 L 32 0 C 34.8 0 37 2.2 37 5 L 37 36 
           L 32 31 L 24 37 L 16 31 L 10 36 L 5 31 Z" 
        fill="#ffffff" 
        stroke="#1a4d65" 
        stroke-width="3.5" 
        stroke-linecap="round" 
        stroke-linejoin="round"
        class="dark:stroke-white dark:fill-gray-900"
      />
      
      <!-- Receipt Lines -->
      <path d="M 12 10 L 25 10" stroke="#1a4d65" stroke-width="3" stroke-linecap="round" class="dark:stroke-white"/>
      <path d="M 12 18 L 30 18" stroke="#1a4d65" stroke-width="3" stroke-linecap="round" class="dark:stroke-white"/>
      <path d="M 12 26 L 18 26" stroke="#1a4d65" stroke-width="3" stroke-linecap="round" class="dark:stroke-white"/>
      
      <!-- Thick Green Checkmark -->
      <!-- The checkmark extends outside the receipt on the right -->
      <path 
        d="M 18 28 L 26 36 L 42 16" 
        fill="none" 
        stroke="#0ea5e9" 
        stroke-width="5" 
        stroke-linecap="round" 
        stroke-linejoin="round"
        class="text-brand-500 stroke-current"
      />
    </g>

    <!-- Text Group -->
    <!-- The font is rendered directly so it scales perfectly -->
    <text 
      x="62" 
      y="35" 
      font-family="'Plus Jakarta Sans', system-ui, sans-serif" 
      font-size="28" 
      font-weight="800" 
      letter-spacing="-0.5"
    >
      <tspan fill="#1a4d65" class="dark:fill-white">Monitor</tspan><tspan fill="#10b981" class="text-brand-500 fill-current">Duit</tspan>
    </text>
  </svg>
</template>

<script setup>
defineProps({
  classes: {
    type: String,
    default: 'h-8 w-auto'
  }
})
</script>
