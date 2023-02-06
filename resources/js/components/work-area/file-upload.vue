<template>
    <div>
        <input type="file" ref="fileInput" @change="onFileSelected"
               :accept="types" :disabled="disabled"
               :multiple="multiple">

        <v-tooltip v-if="icon" color="tooltip" bottom>
            <template v-slot:activator="{ on, attrs }">
                <v-btn icon text :color="color" v-bind="attrs" v-on="on" @change="onFileSelected" @click.native="onFocus">
                    <v-icon>mdi-paperclip</v-icon>
                </v-btn>
            </template>
            <span>Seleccionar</span>
        </v-tooltip>
        <v-btn v-else text :color="color" @change="onFileSelected" @click.native="onFocus">
            <v-icon left dark>mdi-paperclip</v-icon>
            <span>{{ label }}</span>
        </v-btn>

        <v-dialog v-model="dialog" :width="`${filesInfo.length <= 3 ? '500' : ''}`" scrollable persistent @keydown.esc="cancelar">
            <v-card>
                <v-card-title>
                    <v-progress-circular :size="50" :width="5" :rotate="-90" :value="porcentajeDeSubida" class="mr-5" color="primary">
                        {{ porcentajeDeSubida }}
                    </v-progress-circular>
                    <span class="headline">{{ filesInfo.length }} archivos seleccionados:</span>
                </v-card-title>
                <v-card-text>
                    <v-container fluid grid-list-xl>
                        <v-layout wrap justify-space-between>
                            <v-flex v-for="file in filesInfo" :key="file.id" class="d-flex justify-center">
                                <v-hover>
                                    <template v-slot="{ hover }">
                                        <v-card width="400px" shaped :elevation="hover ? 6 : 2">
                                            <v-list-item two-line>
                                                <!--<v-list-item-avatar v-if="file.tipo === 'PNG'"
                                                                    tile
                                                                    size="50"
                                                                    color="grey"
                                                ></v-list-item-avatar>-->

                                                <v-icon :color="file.colorIcon" left>{{ file.icon }}</v-icon>

                                                <v-list-item-content class="pl-1">
                                                    <v-list-item-title>{{ file.nombre }}</v-list-item-title>
                                                    <v-list-item-subtitle>{{ file.tipo }} - {{ file.tamanio }}</v-list-item-subtitle>
                                                </v-list-item-content>

                                                <v-tooltip color="tooltip" top>
                                                    <template v-slot:activator="{ on, attrs }">
                                                        <v-btn icon>
                                                            <v-icon color="red" v-bind="attrs" v-on="on" @click="deleteItem(file)">mdi-delete</v-icon>
                                                        </v-btn>
                                                    </template>
                                                    <span>Remover</span>
                                                </v-tooltip>
                                            </v-list-item>
                                        </v-card>
                                    </template>
                                </v-hover>
                            </v-flex>
                        </v-layout>
                    </v-container>
                </v-card-text>
                <v-card-actions>
                    <v-container fluid>
                        <v-row class="d-flex justify-center">
                            <v-btn
                                color="grey"
                                rounded
                                outlined
                                class="px-5 mr-7"
                                @click.native="cancelar"
                                :disabled="uploading">Cancelar
                            </v-btn>
                            <action-btn
                                color="primary"
                                rounded
                                dark
                                class="px-5"
                                @click.native="guardar"
                                :loading="uploading">
                                <v-icon left class="mr-3">mdi-cloud-upload</v-icon>
                                Adjuntar
                            </action-btn>
                        </v-row>
                    </v-container>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
    export default {
        name: "file-upload",
        props: {
            icon: false,
            label: {
                type: String,
                default: 'Seleccionar'
            },
            disabled: {
                type: Boolean,
                default: false
            },
            multiple: {
                type: Boolean,
                default: false
            },
            maximumFiles: {
                type: Number,
                default: 7
            },
            types: {
                type: String,
                default: 'video/mp4, image/*, application/pdf, .xls, .xlsx, .docx, .doc, .odt, .ppt, .pptx'
            },
            color: {
                type: String,
                default: 'primary'
            },
            info: {
                type: Object,
                default: () => {
                    return {
                        id: '', modulo: '', tipo_id: ''
                    }
                }
            },
            storage: {
                type: String,
                require: true
            }
        },
        data() {
            return {
                dialog: false,
                filesInfo: [],
                theFiles: [],
                porcentajeDeSubida: 0,
                uploading: false
            }
        },
        methods: {
            bytesToSize(bytes) {
                const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
                if (bytes === 0) return 'n/a'
                const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)), 10)
                if (i === 0) return `${bytes} ${sizes[i]})`
                return `${(bytes / (1024 ** i)).toFixed(1)} ${sizes[i]}`
            },
            onFocus() {
                if (!this.disabled) {
                    //debugger;
                    this.$refs.fileInput.click();
                }
            },
            onFileSelected(e) {
                this.filesInfo = [];
                this.theFiles = [];
                if (e.target.files.length > this.maximumFiles)
                    return this.$iziToast.error({
                        title: 'Atención!',
                        message: 'Solo puedes seleccionar un máximo de ' + this.maximumFiles + ' archivos a la vez.',
                    });

                if (e.target.files.length > 0) {
                    this.dialog = true;
                    let files = e.target.files;
                    this.theFiles = Array.from(files);
                    this.getFilesInfo(files);
                }
            },
            getFilesInfo(files) {
                for (var i = 0, f; f = files[i]; i++) {

                    let fileType = f.type.split('/', 2)[1]
                    fileType = this.getFilesType(fileType)

                    const icon = this.getFileIcon(fileType).icon
                    const colorIcon = this.getFileIcon(fileType).color

                    this.filesInfo.push(
                        {
                            id: i,
                            nombre: f.name,
                            tipo: fileType,
                            tamanio: this.bytesToSize(f.size),
                            icon: icon,
                            colorIcon: colorIcon
                        });
                }
                //this.fileInfo = [...this.files].map(file => file.name).join(', ');
            },
            cancelar() {
                this.dialog = false;
                this.filesInfo = [];
                this.theFiles = [];
                this.porcentajeDeSubida = 0;
                this.uploading = false;
                this.$refs.fileInput.value = null;
            },
            guardar() {
                this.$Progress.start();
                this.uploading = true;
                axios.post(`${this.storage}`, this.fillFormData(), {
                    onUploadProgress: uploadEvent => {
                        this.porcentajeDeSubida = Math.round(uploadEvent.loaded / uploadEvent.total * 100)
                    }
                })
                    .then(res => {
                        this.$iziToast.msg(res, this.$Progress);
                        this.$emit('refresh', res.data)
                        this.cancelar();
                    })
                    .catch(err => {
                        this.$iziToast.fail(err, this.$Progress);
                        this.porcentajeDeSubida = 0;
                        this.uploading = false;
                    })
            },
            deleteItem(item) {
                const index = this.filesInfo.indexOf(item)
                confirm('Remover del listado -> ' + item.nombre + '?') && this.theFiles.splice(index, 1) && this.filesInfo.splice(index, 1)
                if (this.filesInfo.length == 0)
                    this.cancelar();
            },
            fillFormData() {
                let fd = new FormData();
                for (var i = 0; i < this.theFiles.length; i++) {
                    let file = this.theFiles[i];
                    fd.append('file[' + i + ']', file)
                }
                //if (this.$helper.hasValue(this.info.modulo)) {
                fd.append('modulo', this.info.modulo)
                fd.append('id', this.info.id)
                fd.append('tipo_id', this.info.tipo_id)
                //}
                return fd
            },
            getFileIcon(tipo) {
                switch (tipo) {
                    case 'powerpoint':
                        return {icon: 'mdi-file-powerpoint-outline', color: 'orange lighten-2'}
                    case 'pdf':
                        return {icon: 'mdi-file-pdf-outline', color: 'red lighten-2'}
                    case 'word':
                        return {icon: 'mdi-file-word-outline', color: 'blue lighten-2'}
                    case 'excel':
                        return {icon: 'mdi-file-excel-outline', color: 'green lighten-2'}
                    case 'imagen':
                        return {icon: 'mdi-file-image-outline', color: 'green lighten-2'}
                    case 'video':
                        return {icon: 'mdi-video-vintage', color: 'blue lighten-2'}
                    default:
                        return {icon: 'mdi-view-dashboard-outline', color: 'grey darken-3'}
                }
            },
            getFilesType(tipo) {
                switch (tipo) {
                    case 'vnd.ms-powerpoint':
                    case 'vnd.openxmlformats-officedocument.presentationml.presentation':
                        return 'powerpoint'
                    case 'msword':
                    case 'vnd.oasis.opendocument.text':
                    case 'vnd.openxmlformats-officedocument.wordprocessingml.document':
                        return 'word'
                    case 'ms-excel':
                    case 'vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                        return 'excel'
                    case 'pdf':
                        return 'pdf'
                    case 'png':
                    case 'jpg':
                    case 'jpeg':
                        return 'imagen'
                    case 'mp4':
                        return 'video'
                    default:
                        return tipo
                }
            }
        }
    }
</script>

<style scoped>
    input[type=file] {
        position: absolute;
        left: -99999px;
    }
</style>
