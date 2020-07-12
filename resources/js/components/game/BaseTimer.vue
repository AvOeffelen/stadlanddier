<template>
    <div>
        <div class="base-timer text-center">
            <svg class="base-timer__svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <g class="base-timer__circle">
                    <circle class="base-timer__path-elapsed" cx="50" cy="50" r="45"></circle>
                    <path
                        :stroke-dasharray="circleDasharray"
                        class="base-timer__path-remaining"
                        :class="remainingPathColor"
                        d="
            M 50, 50
            m -45, 0
            a 45,45 0 1,0 90,0
            a 45,45 0 1,0 -90,0
          "
                    ></path>
                </g>
            </svg>
            <span class="base-timer__label">{{ formattedTimeLeft }}</span>
        </div>
    </div>
</template>

<script>
    const FULL_DASH_ARRAY = 283;
    const WARNING_THRESHOLD = 50;
    const ALERT_THRESHOLD = 20;

    const COLOR_CODES = {
        info: {
            color: "green"
        },
        warning: {
            color: "orange",
            threshold: WARNING_THRESHOLD
        },
        alert: {
            color: "red",
            threshold: ALERT_THRESHOLD
        }
    };

    export default {
        props: [
            'roundTime'
        ],
        data() {
            return {
                timePassed: 0,
                timerInterval: null
            };
        },
        computed: {
            circleDasharray() {
                return `${(this.timeFraction * FULL_DASH_ARRAY).toFixed(0)} 283`;
            },
            formattedTimeLeft() {
                const timeLeft = this.timeLeft;
                const minutes = Math.floor(timeLeft / 60);
                let seconds = timeLeft % 60;

                if (seconds < 10) {
                    seconds = `0${seconds}`;
                }

                if(minutes == 0 && seconds < 60){
                    return `${seconds}`;
                }
                return `${minutes}:${seconds}`;
            },

            timeLeft() {
                return this.roundTime - this.timePassed;
            },

            timeFraction() {
                const rawTimeFraction = this.timeLeft / this.roundTime;
                return rawTimeFraction - (1 / this.roundTime) * (1 - rawTimeFraction);
            },

            remainingPathColor() {
                const {alert, warning, info} = COLOR_CODES;

                if (this.timeLeft <= alert.threshold) {
                    return alert.color;
                } else if (this.timeLeft <= warning.threshold) {
                    return warning.color;
                } else {
                    return info.color;
                }
            }
        },

        watch: {
            timeLeft(newValue) {
                if (newValue === 0) {
                    this.onTimesUp();
                }
            }
        },
        methods: {
            onTimesUp() {
                clearInterval(this.timerInterval);
                this.$root.$emit('timesUp');
            },
            startTimer() {
                this.timerInterval = setInterval(() => (this.timePassed += 1), 1000);
            }
        }
    };
</script>

<style scoped lang="scss">
    $sld-orange: #e55039;

    .base-timer {
        display: block;
        position: relative;
        width: 150px;
        height: 150px;

        &__svg {
            transform: scaleX(-1);
        }

        &__circle {
            fill: none;
            stroke: none;
        }

        &__path-elapsed {
            stroke-width: 7px;
            stroke: #6a89cc;
        }

        &__path-remaining {
            stroke-width: 7px;
            stroke-linecap: round;
            transform: rotate(90deg);
            transform-origin: center;
            transition: 1s linear all;
            fill-rule: nonzero;
            stroke: currentColor;

            &.green {
                color: rgb(65, 184, 131);
            }

            &.orange {
                color: orange;
            }

            &.red {
                color: red;
            }
        }

        &__label {
            position: absolute;
            width: 150px;
            height: 150px;
            top: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            color: $sld-orange;
        }
    }
</style>
