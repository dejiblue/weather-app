<template>
    <div class="text-white mb-8">
        <div class="places-input text-gray-800">
            <input type="search" id="address" class="form-control w-full" placeholder="Where are we going?" />

            <p hidden>Selected: <strong id="address-value">none</strong></p>
        </div>

        <div class="weather-container font-sans  rounded-lg
            w-128 max-w-lg overflow-hidden bg-gray-900 shadow-lg mt-4">

            <div class="current-weather flex items-center justify-between px-6 py-8">
                <div class="flex items-center">
                    <div>
                        <div class="text-6xl font-semibold">{{currentTemperature.actual}}°C</div>
                        <div class="">{{currentTemperature.feels}}°C</div>
                    </div>
                    <div class="mx-5">
                        <div class="font-semifold">{{currentTemperature.summary}}</div>
                        <div class="">{{location.name}}</div>
                    </div>
                </div>
                <div>
                    <canvas ref="iconCurrent" id="iconCurrent" width="96" height="96"></canvas>
                </div>
            </div> <!-- end current weather -->

            <div class="future-weather text-sm bg-gray-800 px-6 py-8 overflow-hidden">

                <div v-for="(day, index) in daily"
                    :key="day.dt"
                    class="flex items-center"
                    :class="{ 'mt-8' : index > 0 }"
                    v-if="index < 5"
                    >

                    <div class="w-1/6 text-lg text-gray-200">{{ toDayOfWeek(day.dt) }}</div>
                    <div class="w-4/6 px-4 flex items-center">
                        <div>
                            <canvas :id="`icon${index+1}`"
                                :data-icon="toKebabCase(day.weather[0].icon)"
                                 width="24" height="24">
                            </canvas>
                        </div>
                        <div class="ml-3">{{ day.weather[0].description }}</div>
                    </div>
                    <div class="w-1/6 text-right">
                        <div>{{  Math.round((day.temp.max)-273.15) }}°C</div>
                        <div>{{  Math.round((day.temp.min)-273.15) }}°C</div>
                    </div>

                </div>

            </div> <!-- end future weather -->

        </div><!-- end weather container -->

    </div>
</template>

<script>
    export default {
        mounted() {
            this.fetchData()

            var placesAutocomplete = places({
                appId: 'plFEUAISVL5S',
                apiKey: 'cd1a1a39af18a4090bba9a7eb622ab8b',
                container: document.querySelector('#address')
            }).configure({
                type: 'city',
                aroundLatLngViaIP: false
            });

            var $address = document.querySelector('#address-value')

            placesAutocomplete.on('change', (e) => {
                $address.textContent = e.suggestion.value
                //console.log(e.suggestion);
                this.location.name = `${e.suggestion.name}, ${e.suggestion.country}`
                this.location.lat = e.suggestion.latlng.lat
                this.location.lng = e.suggestion.latlng.lng
            });
            placesAutocomplete.on('clear', function() {
                $address.textContent = 'none';
            });


        },
        watch: {
            location: {
                handler(newValue, oldValue) {
                    this.fetchData()
                },
                deep: true
            }
        },
        data() {
            return {
                currentTemperature: {
                    actual: '',
                    feels: '',
                    summary: '',
                    icon: ''
                },
                daily: [

                ],
                location: {
                    name: 'Melbourne, Australia',
                    lat: 37.8136,
                    lng: 144.9631
                }
            }
        },
        methods: {
            fetchData(){
                var skycons = new Skycons({"color": "white"});

                fetch(`/api/weather?lat=${this.location.lat}&lng=${this.location.lng}`)
                .then(response => response.json())
                .then(data => {
                    this.currentTemperature.actual = Math.round(data.current.temp-273.15)
                    this.currentTemperature.feels = Math.round(data.current.feels_like-273.15)
                    this.currentTemperature.summary = data.current.weather[0].description
                    this.currentTemperature.icon = this.toKebabCase(data.current.weather[0].icon)

                    this.daily = data.daily

                    skycons.add("iconCurrent", this.currentTemperature.icon)
                    skycons.play()

                    this.$nextTick(() => {
                        skycons.add("icon1",document.getElementById('icon1').getAttribute('data-icon'))
                        skycons.add("icon2",document.getElementById('icon2').getAttribute('data-icon'))
                        skycons.add("icon3",document.getElementById('icon3').getAttribute('data-icon'))
                        skycons.add("icon4",document.getElementById('icon4').getAttribute('data-icon'))
                        skycons.add("icon5",document.getElementById('icon5').getAttribute('data-icon'))
                    })

                })
            },
            toKebabCase(stringToConvert){

                if (stringToConvert == "01d") {
                    stringToConvert = "clear-day"
                }  else if (stringToConvert == "01n") {
                    stringToConvert = "clear-night"
                }  else if (stringToConvert == "02d") {
                    stringToConvert = "cloudy"
                }  else if (stringToConvert == "02n") {
                    stringToConvert = "cloudy"
                }  else if (stringToConvert == "03d") {
                    stringToConvert = "partly-cloudy-day"
                }  else if (stringToConvert == "03n") {
                    stringToConvert = "partly-cloud-night"
                }  else if (stringToConvert == "04d") {
                    stringToConvert = "cloudy"
                }  else if (stringToConvert == "04n") {
                    stringToConvert = "cloudy"
                }  else if (stringToConvert == "09d") {
                    stringToConvert = "rain"
                }  else if (stringToConvert == "09n") {
                    stringToConvert = "rain"
                }  else if (stringToConvert == "10d") {
                    stringToConvert = "sleet"
                }  else if (stringToConvert == "10n") {
                    stringToConvert = "sleet"
                }  else if (stringToConvert == "11d") {
                    stringToConvert = "thunder"
                }  else if (stringToConvert == "11n") {
                    stringToConvert = "thunder"
                }  else if (stringToConvert == "13d") {
                    stringToConvert = "snow"
                }  else if (stringToConvert == "13n") {
                    stringToConvert = "snow"
                }

                return stringToConvert

            },
            toDayOfWeek(timestamp) {
                const newDate = new Date(timestamp * 1000 )
                const days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT']
                return days[newDate.getDay()]
            }
        },
    }
</script>
