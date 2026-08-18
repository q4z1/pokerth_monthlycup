<template>
    <div>
        <h1 class="page-title">{{ monthName }} Cup {{ year }}</h1>
        <p class="page-subtitle">Table results</p>

        <el-empty v-if="!tables.length" description="No results found for this cup." />

        <div class="panel" v-for="table in tables" :key="table.key">
            <h2 class="section-title">{{ table.title }}</h2>
            <el-table :data="table.rows" style="width:100%" stripe
                      :row-class-name="() => 'row-' + table.variant">
                <el-table-column prop="position" label="Place" width="90" sortable />
                <el-table-column label="Player">
                    <template #default="scope">
                        <a class="player-link" target="_blank" rel="noopener"
                           :href="playerUrl + encodeURIComponent(scope.row.playername)">
                            {{ scope.row.playername }}
                        </a>
                    </template>
                </el-table-column>
                <el-table-column prop="points" label="Points" width="110" sortable />
            </el-table>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        year: { type: Number, required: true },
        month: { type: Number, required: true },
        monthName: { type: String, required: true },
        tables: { type: Array, default: () => [] },
        playerUrl: { type: String, required: true },
    },
};
</script>
