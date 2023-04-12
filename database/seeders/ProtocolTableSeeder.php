<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class ProtocolTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wh_protocol')->insert([
            [
                'id'              => 1,
                'idtype'          => 4,                     
                'name'            => 'Lavado de manos',     
                'version'         => '1.0',   
                'smallimage'      => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/small/protocolo_limpieza_manos.png'          ,
                'image'           => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/protocolo_limpieza_manos.png',  
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 2,
                'idtype'          => 1,                            
                'name'            => 'Limpieza de Habitaciones',     
                'version'         => '1.0',    
                'smallimage'      => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/small/protocolo_limpieza_habitaciones.png',          
                'image'           => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/protocolo_limpieza_habitaciones.png',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 3,
                'idtype'          => 3,                         
                'name'            => 'Manipulación de blancos',     
                'version'         => '1.0',      
                'smallimage'      => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/small/protocolo_manipulacion_blancos.png',      
                'image'           => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/protocolo_manipulacion_blancos.png',  
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 4,
                'idtype'          => 2,                           
                'name'            => 'Limpieza de recepción',     
                'version'         => '1.0',       
                'smallimage'      => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/small/protocolo_limpieza_recepcion.png',
                'image'           => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/protocolo_limpieza_recepcion.png',  
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 5,
                'idtype'          => 2,                           
                'name'            => 'Limpieza de baños públicos',     
                'version'         => '1.0',    
                'smallimage'      => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/small/protocolo_limpieza_banos.png',       
                'image'           => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/protocolo_limpieza_banos.png',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 6,
                'idtype'          => 6,                           
                'name'            => 'Uso de áreas comunes',     
                'version'         => '1.0',          
                'smallimage'      => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/small/protocolo_uso_areas_comunes.png' ,
                'image'           => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/protocolo_uso_areas_comunes.png',  
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 7,
                'idtype'          => 3,                           
                'name'            => 'Limpieza y Desinfección',     
                'smallimage'      => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/small/protocolo_limpieza_y_desinfeccion.png',
                'image'           => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/protocolo_limpieza_desinfeccion.png',
                'version'         => '1.0',                
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 8,
                'idtype'          => 4,                           
                'name'            => 'Forma correcta de toser y estornudar',     
                'version'         => '1.0',   
                'smallimage'      => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/small/protocolo_estornudo.png'        ,
                'image'           => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/protocolo_estornudo.png',
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 9,
                'idtype'          => 6,                           
                'name'            => 'Uso de Piscinas',     
                'version'         => '1.0',                
                'smallimage'      => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/small/protocolo_uso_piscina.png',
                'image'           => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/protocolo_uso_piscina.png',  
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            [
                'id'              => 10,
                'idtype'          => 6,                           
                'name'            => 'Uso del Restaurante',     
                'version'         => '1.0', 
                'smallimage'      => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/small/protocolo_uso_restaurantes.png',
                'image'           => 'https://dingdonecdn.nyc3.digitaloceanspaces.com/general/protocolos/protocolo_uso_restaurante.png',  
                'created_at'      => Carbon::now(),
                'updated_at'      => Carbon::now()
            ],
            
        ]);
    }
}
