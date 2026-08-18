<template>
    <div>
        <h1 class="page-title">Cup Ranking Points {{ year }}</h1>
        <p class="page-subtitle">How many points each finishing place is worth</p>

        <div class="panel">
            <el-table :data="rows" style="width:100%" stripe>
                <el-table-column prop="place" label="Place" width="100" />
                <el-table-column prop="first" label="1st Round" />
                <el-table-column prop="bronze" label="Bronze" />
                <el-table-column prop="silver" label="Silver" />
                <el-table-column prop="gold" label="Gold" />
            </el-table>
        </div>

        <div class="panel">
            <h2 class="section-title">Score</h2>
            <p>1 point for participation + first round points + final round points.</p>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        year: { type: Number, required: true },
        rows: { type: Array, default: () => [] },
    },
};
</script>
