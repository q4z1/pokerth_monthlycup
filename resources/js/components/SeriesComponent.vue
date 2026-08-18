<template>
    <div>
        <h1 class="page-title">Series {{ year }} Results</h1>
        <p class="page-subtitle">Winners of every gold final table</p>

        <el-empty v-if="!cups.length" description="No results for this season yet." />

        <div class="panel" v-for="cup in cups" :key="cup.month">
            <h2 class="section-title">{{ cup.month_name }} Cup</h2>
            <p class="page-subtitle" style="margin-bottom:1rem" v-if="cup.date">
                {{ formatDate(cup.date) }}
                <template v-if="cup.forum_link">
                    &middot; <el-link :href="cup.forum_link" target="_blank" rel="noopener">Forum thread</el-link>
                </template>
            </p>

            <div class="podium">
                <div v-for="place in cup.places" :key="place.position">
                    <img v-if="place.award_url" :src="place.award_url" :alt="place.award_label" loading="lazy">
                    <div v-else style="font-size:2rem">{{ medal(place.position) }}</div>
                    <a class="player-link podium-name" target="_blank" rel="noopener"
                       :href="playerUrl + encodeURIComponent(place.playername)">
                        {{ place.playername }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        year: { type: Number, required: true },
        cups: { type: Array, default: () => [] },
        playerUrl: { type: String, required: true },
    },
    methods: {
        medal(position) {
            return ['🥇', '🥈', '🥉'][position - 1] || '';
        },
        formatDate(value) {
            const date = new Date(value.replace(' ', 'T'));
            if (Number.isNaN(date.getTime())) return value;
            return date.toLocaleDateString('en-GB', {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
            });
        },
    },
};
</script>
