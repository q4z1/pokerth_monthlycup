<template>
    <div style="max-width:420px; margin:2rem auto">
        <h1 class="page-title">Login</h1>
        <div class="panel">
            <!--
                No native <form> here on purpose: el-form already renders one,
                and a nested form would swallow the fields on submit.
            -->
            <el-form label-position="top" @submit.prevent="submit">
                <el-form-item label="Username" :error="fieldError('username')">
                    <el-input v-model="form.username" autofocus @keyup.enter="submit" />
                </el-form-item>
                <el-form-item label="Password" :error="fieldError('password')">
                    <el-input v-model="form.password" type="password" show-password
                              @keyup.enter="submit" />
                </el-form-item>
                <el-form-item>
                    <el-checkbox v-model="form.remember">Remember me</el-checkbox>
                </el-form-item>
            </el-form>
            <div style="text-align:right">
                <el-button type="primary" :loading="busy" @click="submit">Login</el-button>
            </div>
        </div>
    </div>
</template>

<script>
import { ElMessage } from 'element-plus';

export default {
    props: {
        action: { type: String, required: true },
        errors: { type: Object, default: () => ({}) },
    },
    data() {
        return {
            form: { username: '', password: '', remember: false },
            fieldErrors: { ...this.errors },
            busy: false,
        };
    },
    methods: {
        fieldError(field) {
            const messages = this.fieldErrors[field];
            return messages ? messages[0] : '';
        },
        submit() {
            if (this.busy) return;
            this.fieldErrors = {};
            this.busy = true;

            window.axios.post(this.action, this.form)
                .then(({ data }) => { window.location.href = data.redirect; })
                .catch((err) => {
                    const data = err.response?.data;
                    if (data?.errors) {
                        this.fieldErrors = data.errors;
                    } else {
                        ElMessage.error(data?.message || 'Login failed.');
                    }
                })
                .finally(() => { this.busy = false; });
        },
    },
};
</script>
