@extends('layouts.app')

<title>{{ $file->name }} - {{ env('APP_NAME_FULL') }}</title>

@section('css')
<style>
    .file-page {
        display: none;
        min-height: calc(100vh - 300px);
    }
</style>
@endsection

@section('content')
<div class="file-page mt-5 mb-5" id="download-file">
    <div class="container">
        <div class="card mb-3">
            <div class="card-body">
              
            <div class="container-fluid">
             <div class="row">
               <div class="col-sm-6">   
                <h4>File info</h4>
                <p>
                    <i class="far fa-file-alt mr-2"></i>
                    <span>File name: {{ $file->name }}</span>
                    <small>({{ $file->file_type }})</small>
                </p>
                <p>
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span>Uploader: {{ $file->user->name }}</span>
                </p> 
                <p>
                    <i class="far fa-save mr-2"></i>
                    <span>File size: {{ number_format($file->file_size, 0, ',', '.') }} KB</span>
                </p>
                <p>
                    <i class="fas fa-cloud-download-alt"></i>
                    <span>Downloads: {{ number_format($file->total_download, 0, ',', '.') }}</span>
                </p>
                <p>
                    <i class="far fa-clock mr-2"></i>
                    <span>Upload time: {{ $file->created_at }}</span>
                </p>
                 
                </div>
               <div class="col-sm-6">
    				<p>Advertising here</p>
                 
                 
                 
               </div></div>      
                     
                <div class="row">
                    <div class="col-sm-4 mb-3" v-for="(file, indexFile) in files" :key="indexFile">
                        <button class="btn btn-primary btn-lg btn-block clearfix" v-if="file.loading">
                            <i class="fas fa-spinner fa-pulse fa-2x mt-2 float-left"></i>
                            <div class="float-right text-right">
                                <div v-if="file.downloaded">
                                    <div class="progress" style="margin: 7px 0; width: 170px">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                                            role="progressbar" :aria-valuenow="file.downloaded" aria-valuemin="0"
                                            aria-valuemax="100" :style="{width: `${file.downloaded}%`}">
                                            @{{ `${file.downloaded}%` }}
                                        </div>
                                    </div>
                                </div>
                                <div v-else>Loading ...</div>
                                <small>Server #@{{ indexFile + 1 }}</small>
                            </div>
                        </button>
                        <button class="btn btn-primary btn-lg btn-block clearfix" :class="{'btn-danger': file.error}"
                            v-else v-on:click="download(file)">
                            <i class="fas fa-cloud-download-alt fa-2x mt-2 float-left"></i>
                            <div class="float-right text-right">
                                <div v-if="file.error">Server error</div>
                                <div v-else>Unlimited download</div>
                                <small>Server #@{{ indexFile + 1 }}</small>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" v-if="showOriginalLink">
            <div class="card-body">
                <h4>If you can't download it, you can try the backup link</h4>
                <ul>
                    <li v-for="(file, indexFile) in files" :key="indexFile">
                        <a target="_blank" :href="`https://drive.google.com/file/d/${file.id}/view`">
                            @{{ `https://drive.google.com/file/d/${file.id}/view` }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/vue"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    new Vue({
        el: '#download-file',
        data() {
            return {
                files: {!! json_encode($uploadedFiles) !!},
                showOriginalLink: false
            }
        },
        mounted() {
            document.getElementById('download-file').style.display = 'block'
            this.files.map(file => {
                this.$set(file, 'downloaded', 0)
                this.$set(file, 'loading', false)
                this.$set(file, 'error', false)
            })
        },
        methods: {
            download: function(file) {
                if (!file.loading) {
                    file.loading = true
                    axios.get(
                        `https://www.googleapis.com/drive/v3/files/${file.id}?alt=media&source=downloadUrl`,
                        {
                            headers: {
                                Authorization: `Bearer ${file.token}` 
                            },
                            responseType: 'blob',
                            onDownloadProgress: (progressEvent) => {
                                const totalLength = progressEvent.lengthComputable ? progressEvent.total :
                                    progressEvent.target.getResponseHeader('content-length') ||
                                    progressEvent.target.getResponseHeader('x-decompressed-content-length');
                                if (totalLength !== null) {
                                    const downloaded = Math.round((progressEvent.loaded * 100) / totalLength);
                                    if (downloaded) file.downloaded = downloaded
                                }
                            },
                        }
                    ).then(response => {
                        if (response) {
                            const url = window.URL.createObjectURL(new Blob([response.data]));
                            const link = document.createElement('a');
                            link.href = url;
                            link.setAttribute('download', file.name);
                            document.body.appendChild(link);
                            link.click();
                            this.updateDownload()
                        }
                        file.loading = false
                    }).catch(error => {
                        console.log('download error', error)
                        file.error = true
                        file.loading = false
                        this.showOriginalLink = true
                    })
                }
            },
            updateDownload: function() {
                axios.get('/file/download/{{ $file->code }}')
            }
        }
    });
</script>
@endsection