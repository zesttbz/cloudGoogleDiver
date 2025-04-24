@extends('layouts.app')


<title>{{ env('APP_NAME_FULL') }} - Upload</title>

@section('css')
<style>
    .upload-page {
        display: none;
        min-height: calc(100vh - 300px);
    }

    .list-upload {
        max-height: 400px;
        overflow-y: auto;
    }
</style>
@endsection

@section('content')
<div class="upload-page mt-5 mb-5" id="upload-form">
    <div class="container">
        <div class="card mb-5">
            <div class="card-header clearfix">
                <div class="float-left" style="font-size: 25px">Upload file</div>
                <div class="float-right">
                    <label class="btn btn-outline-primary mr-2 mb-0" style="width: 150px" v-if="accounts.length">
                        Select file
                        <input type="file" multiple class="d-none" v-on:input="changeFileInput">
                    </label>
                    <button class="btn btn-outline-success" style="width: 150px" v-if="files.length"
                        v-on:click="upload">
                        Start
                        <span>
                            @{{ files.length }}
                            file
                        </span>
                    </button>
                </div>
            </div>
            <ul class="list-group list-group-flush list-upload" v-if="files.length">
                <li class="list-group-item" v-for="(file, indexFile) in files" :key="indexFile">
                    <div class="row">
                        <div class="col-7">
                            <div class="mb-1">
                                @{{ file.name }} -
                                <small>@{{ file.type }} - @{{ file.size.toLocaleString("vi") }} KB</small>
                            </div>
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                    :class="file.bgStatus" :aria-valuenow="file.uploaded" aria-valuemin="0"
                                    aria-valuemax="100" :style="{width: `${file.uploaded}%`}">
                                    @{{ file.text }}
                                </div>
                            </div>
                        </div>
                        <div class="col-5 text-right">
                            <button class="btn btn-outline-danger btn-sm mt-2"
                                v-on:click="deleteFile(indexFile)">Remove</button>
                        </div>
                    </div>
                </li>
            </ul>
            <ul class="list-group list-group-flush" v-else>
                <div class="text-center">
                    <i class="fas fa-cloud-upload-alt" style="margin: 40px 0px;font-size: 70px;color: #9e9e9e"></i>
                </div>
            </ul>
        </div>
        <div class="card" v-if="uploadedFiles.length">
            <div class="card-header clearfix">
                <div class="float-left" style="font-size: 25px">Uploaded file</div>
            </div>
            <ul class="list-group list-group-flush list-upload">
                <li class="list-group-item" v-for="(file, indexFile) in uploadedFiles" :key="indexFile">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-1">
                                @{{ file.name }} -
                                <small>@{{ file.type }} - @{{ file.size.toLocaleString("vi") }} KB</small>
                            </div>
                        </div>
                        <div class="col-6 text-right">
                            <a v-if="file.code" class="btn btn-outline-primary btn-sm mt-2" :href="`/file/${file.code}`"
                                target="_blank">Download</a>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/vue"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    new Vue({
        el: '#upload-form',
        data: {
            files: [],
            uploadedFiles: [],
            accounts: {!! json_encode($accounts) !!}
        },
        mounted() {
            document.getElementById('upload-form').style.display = 'block'
        },
        methods: {
            changeFileInput(event) {
                const input = event.target
                
                if (input.files && input.files.length) {
                    for (var i = 0; i < input.files.length; i++) {
                        this.files.push({
                            name: input.files[i].name || 'filename',
                            type: input.files[i].type || 'application/octet-stream',
                            size: Math.round(input.files[i].size / 1024),
                            content: input.files[i],
                            bgStatus: '',
                            text: '0%',
                            uploaded: 0,
                            uploads: this.accounts.map(item => ({
                                google_id: '',
                                token: item.token,
                                account_id: item.id,
                            }))
                        })
                    }
                }
                input.value = null
            },
            deleteFile(index) {
                this.files.splice(index, 1)
            },
            async upload() {
                if (confirm(`You want to upload ${this.files.length} file?`)) {
                    this.files.map(async (file) => {
                        file.bgStatus = 'bg-warning'
                        file.text = 'Pending...'
                        file.uploaded = 100
                        const fileId = await this.uploadFirstAccount(file)

                        if (fileId) {
                            await Promise.all(
                                file.uploads.map((item, index) => {
                                    if (index) return this.copyFile(fileId, item)
                                })
                            )

                            await this.saveFile(file)
                        }
                    })
                }
            },
            async uploadFirstAccount(file) {
                try {
                    const uploadId = await this.getUploadId(file.name, file.type, file.uploads[0].token)
                    if (uploadId) {
                        const response = await axios.put(
                            'https://www.googleapis.com/upload/drive/v3/files/?uploadType=resumable&upload_id=' + uploadId,
                            file.content,
                            {
                                headers: { "Content-Type": "text/plain" },
                                onUploadProgress: (progressEvent) => {
                                    const totalLength = progressEvent.lengthComputable ? progressEvent.total :
                                        progressEvent.target.getResponseHeader('content-length') ||
                                        progressEvent.target.getResponseHeader('x-decompressed-content-length');
                                    if (totalLength !== null) {
                                        const uploaded = Math.round((progressEvent.loaded * 100) / totalLength);
                                        if (uploaded) {
                                            file.bgStatus = 'bg-primary'
                                            file.uploaded = uploaded
                                            file.text = `${file.uploaded}%`
                                        }
                                    }
                                },
                            }
                        )
                        file.uploads[0].google_id = response.data.id
                        await this.shareFile(response.data.id, file.uploads[0].token)
                        return response.data.id
                    } else {
                        file.bgStatus = 'bg-danger'
                        file.text = 'Error'
                        file.uploaded = 100
                    }
                } catch (error) {
                    console.log('upload error', error)
                    file.bgStatus = 'bg-danger'
                    file.text = 'Error'
                    file.uploaded = 100
                }
            },
            async getUploadId(name, type, token) {
                try {
                    const response = await axios.post('https://www.googleapis.com/upload/drive/v3/files/?uploadType=resumable', {
                        name: name,
                        mimeType: type
                    }, {
                        headers: {
                            Authorization: `Bearer ${token}` 
                        }
                    })
                    if (response && response.headers && response.headers['x-guploader-uploadid'])
                        return response.headers['x-guploader-uploadid']
                } catch (error) {
                    console.log('get upload id error', error)
                }
            },
            async shareFile(fileId, token) {
                try {
                    await axios.post(`https://www.googleapis.com/drive/v3/files/${fileId}/permissions`, {
                        "role": "reader",
                        "type": "anyone"
                    }, {
                        headers: {
                            Authorization: `Bearer ${token}` 
                        }
                    })
                } catch (error) {
                    console.log('share file error', error)
                }
            },
            async copyFile(fileId, item) {
                try {
                    const response = await axios.post(`https://www.googleapis.com/drive/v2/files/${fileId}/copy`, null, {
                        headers: {
                            Authorization: `Bearer ${item.token}` 
                        }
                    })
                    item.google_id = response.data.id
                    await this.shareFile(response.data.id, item.token)
                } catch (error) {
                    console.log('copy file error', error)
                }

            },
            async saveFile(file) {
                const response = await axios.post('/dashboard/upload', {
                    name: file.name,
                    file_type: file.type,
                    file_size: file.size,
                    uploads: file.uploads.filter(item => item.google_id).map(item => ({
                        id: item.google_id,
                        account_id: item.account_id,
                    }))
                })
                file.uploaded = 100
                file.text = '100%'
                file.bgStatus = 'bg-success'
                file.code = response.data.data.code
                setTimeout(() => {
                    this.uploadedFiles.push(file)
                    this.files.splice(this.files.indexOf(file), 1)
                }, 1000);
            }
        }
    });
</script>
@endsection