<?php 

namespace App\Traits;
use App\Models\Address;

trait HasAddresses {

    /**
     * Relationships
     */
    public function addresses() {
        return $this->morphMany(Address::class, 'addressable');
    }

    /**
     * Mutators
     */
    public function getAddress($type) {
        $address = $this->addresses()->where('type', $type)->first();
        if (!$address) {
            $address = $this->addresses()->create([
                'type' => $type,
                'is_primary' => true
            ]);
        }
        return $address;
    }

    public function getBillingAddressAttribute() {
        return $this->addresses()->where('type', 'Billing')->firstOrCreate([
            'type' => 'Billing',
            'is_primary' => true
        ]);
    }
    
    public function getShippingAddressAttribute() {
        return $this->addresses()->where('type', 'Shipping')->firstOrCreate([
            'type' => 'Shipping',
            'is_primary' => true
        ]);
    }

    /**
     * Helpers
     */
    public function getValidBillingAddresses() {
        $addresses = $this->addresses()->where('type', 'Billing')->get();

        $filtered = $addresses->filter(function($address, $key){

            return $address->first_name && $address->last_name && $address->address_line_1 && $address->state && $address->city && $address->country && $address->postal_code && $address->email_address && $address->phone;

        });

        return $filtered;
    }
    
    public function saveNewBillingAddress() {
        $address = new Address;

        $address->type = 'Billing';
        $address->first_name = request('billing_first_name');
        $address->last_name = request('billing_last_name');
        $address->address_line_1 = request('billing_address_line_1');
        $address->address_line_2 = '';
        $address->city = request('billing_city');
        $address->postal_code = request('billing_postal_code');
        $address->country = request('billing_country');
        $address->state = request('billing_state');
        $address->phone = request('billing_phone');
        $address->email_address = request('billing_email_address');
        $address->save();
        
        $this->addresses()->save($address);
        
        return $address;
    }

    //update primary billing
    public function saveBillingAddress() {
        $address = $this->billing_address;

        $address->address_line_1 = request('billing_address_line_1');
        $address->address_line_2 = request('billing_address_line_2');
        $address->city = request('billing_city');
        $address->postal_code = request('billing_postal_code');
        $address->country = request('billing_country');
        $address->state = request('billing_state');
        $address->save();
        
        $this->addresses()->save($address);
    }
    
    //save primary shipping
    public function saveShippingAddress() {
        $address = $this->shipping_address;

        $address->address_line_1 = request('shipping_address_line_1');
        $address->address_line_2 = request('shipping_address_line_2');
        $address->city = request('shipping_city');
        $address->postal_code = request('shipping_postal_code');
        $address->country = request('shipping_country');
        $address->state = request('shipping_state');
        $address->save();
        
        $this->addresses()->save($address);
    }

    //save billing to shipping
    public function saveShippingToBillingAddress() {
        $address = $this->billing_address;

        $address->address_line_1 = request('shipping_address_line_1');
        $address->address_line_2 = request('shipping_address_line_2');
        $address->city = request('shipping_city');
        $address->postal_code = request('shipping_postal_code');
        $address->country = request('shipping_country');
        $address->state = request('shipping_state');
        $address->save();
        
        $this->addresses()->save($address);
    }

    // get select options
    public function getAddressesSelectOption($type) {
        $options = [];
        foreach ($this->addresses()->where('type', $type)->orderBy('is_primary', 'DESC')->get() as $key => $address) {
            $options[] = ['label' => $address->address_line_1 .' '.$address->city.' '.$address->state.' '.$address->postal_code.' '.$address->country.' '.$address->email_address.' '.$address->phone_number, 'value' => $address->id];
        }
        return $options;
    }

}