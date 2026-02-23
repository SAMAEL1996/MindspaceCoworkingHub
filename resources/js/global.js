document.addEventListener('DOMContentLoaded', function () {
    setInterval(function () {
        fetch('/check-latest-rfid')
            .then(response => response.json())
            .then(data => {

                if (!data.success) return;

                Livewire.dispatch('openEndTimeModal', {
                    dailySaleId: data.daily_sale_id
                });

            })
            .catch(error => console.log(error));
    }, 500);
});