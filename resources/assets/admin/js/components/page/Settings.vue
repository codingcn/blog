<template>
    <section class="main">
        <div class="crumbs">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
                <el-breadcrumb-item :to="{ path: '/settings' }">系统设置</el-breadcrumb-item>
            </el-breadcrumb>
        </div>
        <div class="form-box" v-loading.body="loading">
            <el-form ref="form" :model="form" label-width="80px">
                <el-form-item label="站点名称">
                    <el-input v-model="form.site_title"></el-input>
                </el-form-item>
                <el-form-item label="IPC备案号">
                    <el-input v-model="form.site_icp"></el-input>
                </el-form-item>
                <el-form-item label="网站LOGO">
                    <el-upload
                            list-type="picture-card"
                            name="site_logo"
                            class="upload-cover"
                            :multiple="false"
                            :on-preview="handlePictureCardPreview"
                            :on-change="handleChange"
                            :on-remove="handleRemove"
                            :file-list="file_list"
                            :on-success="handleUploadSuccess"
                            :headers="headers"
                            v-bind:action=" upload_action_logo"
                    >
                        <i class="el-icon-plus"></i>
                        <el-dialog v-model="dialogVisible">
                            <img width="100%" :src="dialogImageUrl" alt="">
                        </el-dialog>
                    </el-upload>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="onSubmit">提交</el-button>
                    <el-button>取消</el-button>
                </el-form-item>
            </el-form>
        </div>
    </section>
</template>

<script>
    import qs from 'qs';

    export default {
        beforeMount() {
            this.getSettings()
        },
        data: function () {
            return {
                loading: false,
                form: {},
                upload_action_logo: this.$difines.root_url + '/api/admin/settings/upload-logo',
                file_list: [],
                dialogImageUrl: '',
                dialogVisible: false,
                headers: {
                    Authorization: 'Bearer ' + this.$auth.getToken()
                }
            }
        },
        methods: {
            getSettings() {
                this.loading = true
                this.$axios({
                    url: this.$difines.root_url + '/api/admin/settings',
                    method: 'GET',
                }).then(response => {
                    let data = response.data.data
                    this.form = data
                    if (data.site_logo === '') {
                        this.file_list.splice(0)
                    } else {
                        this.file_list.push({
                            name: 'LOGO',
                            url: data.site_logo,
                        })
                    }
                    this.loading = false
                }).catch(response => {
                    // console.log(response)
                    this.loading = false
                });
            },
            handleChange(file, file_list) {
                this.file_list.splice(0)
                this.file_list.push(file)
            },
            handleRemove(file, file_list) {
                this.$axios({
                    url: this.$difines.root_url + '/api/admin/settings/upload-logo-delete',
                    method: 'POST',
                    data: {
                        site_logo: file.url
                    }
                }).then(response => {
                    if (response.data.err_no !== 0) {
                        this.$notify.error({
                            title: '错误',
                            message: '移除文件出错了'
                        });
                    } else {
                        this.$notify.success({
                            title: '成功',
                            message: '移除文件成功'
                        });
                        this.form.site_logo = ''
                    }
                }).catch(response => {
                });
            },
            handlePictureCardPreview(file) {
                // console.log(file);
            },
            handleUploadSuccess(response, file, fileList) {
                this.file_list.splice(0)
                this.file_list.push(file)
                this.form.site_logo = response.data.site_logo
            },
            onSubmit() {
                console.log(this.form)
                this.$axios({
                    url: this.$difines.root_url + '/api/admin/settings',
                    method: 'PUT',
                    data: qs.stringify(this.form)
                }).then(response => {
                    if (response.data.err_no === 0) {
                        this.$notify.success({
                            title: '成功',
                            message: response.data.err_msg
                        });
                        this.$router.push({path: '/settings'})
                    } else {
                        this.$notify.error({
                            title: '失败',
                            message: response.data.err_msg
                        });
                    }
                }).catch(response => {
                });
            }
        }
    }
</script>
<style>
    .el-upload-dragger {
        width: auto;
        height: auto;
    }
</style>