<template>
    <div>
        <b-row class="p-5">
            <b-col md="12" lg="7" sm="12" align-self="center">
                <b-card
                    header-tag="header"
                    header-class=""
                    header-bg-variant="dark-sapphire-lighter"
                    header-text-variant="white"
                    footer-tag="footer"
                    class="mb-2 text-center"
                >
                    <template v-slot:header>
                        <div class="header-card">
                            <div class="text-center"><em>Start game</em></div>
                        </div>
                    </template>
                    <b-card-text body-bg-variant="dark-sapphire">
                        <div class="text-left pt-3">
                            <label for="username">Username</label>
                            <b-form-input id="username" v-model="game.players[0].name"></b-form-input>
                        </div>
                        <div class="text-left pt-3">
                            <label for="amount-rounds">Hoeveel rondes wil je spelen?</label>
                            <b-select id="amount-rounds" v-model="game.rounds" :options="roundOptions"></b-select>
                        </div>
                        <div class="text-left pt-3">
                            <label for="amount-rounds">Ronde tijd</label>
                            <b-select id="amount-rounds" v-model="game.timePerRound" :options="timeOptions"></b-select>
                        </div>
                        <div class="text-left pt-3">
                            <label for="option-list">Selecteer onderwerpen</label>
                            <ul id="option-list" class="option-list">
                                <li v-for="(option,index) in this.game.options">
                                    <input type="checkbox" class="form-checkbox" :value="option"
                                           v-model="game.selectedOptions" />
                                    {{option}}
                                </li>
                                <li class="disabled"><input  type="checkbox" disabled /> artiest (coming soon!)</li>
                                <li class="disabled"><input  type="checkbox" disabled /> acteur (coming soon!)</li>
                            </ul>
                        </div>
                        <div class="text-left pt-3">
                            <label for="public_lobby">Publieke lobby</label>
                            <ul id="public_lobby" class="option-list">
                                <li class="disabled"><input  type="checkbox" v-model="game.public_lobby" value="off" disabled/>&nbsp;Privé spel (coming soon!)</li>
                            </ul>
                        </div>
                        <b-button block variant="dark-sapphire-lighter" @click="createGame">create</b-button>
                    </b-card-text>
                </b-card>
            </b-col>
        </b-row>
    </div>
</template>

<script>
    export default {
        name: "GameInit",
        data() {
            return {
                game:{
                    players: {
                        0:{
                            name:'NewPlayer',
                            is_owner:1,
                            location:'',
                            ready:0,
                            connected:0
                        }
                    },
                    selectedOptions:[
                        // 'hoofdstad',
                        'land',
                        'dier',
                        'sport'
                    ],
                    options:[
                        'hoofdstad',
                        'land',
                        'dier',
                        'sport',
                        'job',
                        'auto',
                        'merk'
                        // 'artiest',
                        // 'acteur',
                    ],
                    rounds:2,
                    generatedLetters:null,
                    timePerRound:10,
                    public_lobby:true
                },
                roundOptions:[
                    { value:1,text:"1"},
                    { value:2,text:"2"},
                    { value:3,text:"3"},
                    { value:4,text:"4"},
                    { value:5,text:"5"},
                    { value:6,text:"6"},
                    { value:7,text:"7"},
                    { value:8,text:"8"},
                    { value:9,text:"9"},
                    { value:10,text:"10"},
                ],
                timeOptions:[
                    {value:10,text:"10 sec"},
                    {value:90,text:"90 sec"},
                    {value:120,text:"120 sec"},
                    {value:150,text:"150 sec"},
                    {value:180,text:"180 sec"},
                ],
                createdGameData:null,
            };
        },
        created() {
        },
        methods: {
            createGame(){
                axios.post('/game/create-game',this.game)
                .then( response => {
                    this.createdGameData = response.data;
                    this.loadGameLobby(this.createdGameData);
                });
            },
            loadGameLobby(game){
                let url = '/game/'+ game.key+'/lobby';
                window.location = url;
            }
        }
    }
</script>

<style scoped>
    .option-list{
        list-style-type: none;
    }
</style>
