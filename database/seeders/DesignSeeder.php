<?php

namespace Database\Seeders;

use App\Models\Diseno;
use App\Models\RevisionDiseno;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class DesignSeeder extends Seeder
{
    public function run(): void
    {
        $fabrica = Usuario::where('correo', 'fabrica@apukuntur.test')->first();
        $eu = Usuario::where('correo', 'eu@apukuntur.test')->first();

        $diseno = Diseno::create([
            'codigo' => 'AK-D-001',
            'nombre' => 'Sweater Baby Alpaca',
            'responsable' => 'María Quispe',
            'fecha_inicio' => '2026-03-10',
            'version' => 2,
            'estado_aprobacion' => 'en_desarrollo',
            'aprobado_para_produccion' => false,
        ]);

        $diseno->idea()->create([
            'tipo_prenda' => 'Sweater',
            'temporada' => 'Otoño 2026',
            'paleta' => 'Crema / Beige',
            'tipo_tejido' => 'Punto inglés',
            'descripcion' => 'Sweater clásico de baby alpaca con acabado suave y tono neutro.',
            'codigo_hilado' => 'ALP-2/28',
            'titulo_hilado' => '2/28',
            'composicion' => '100% Baby Alpaca',
            'nombre_modelo' => 'Chompa Solana',
        ]);

        $diseno->plasmado()->create([
            'material' => '100% Baby Alpaca',
            'hilado' => 'Alpaca fino 2/28',
            'titulo' => 'Sweater clásico',
            'galga' => '2,54 cm',
            'tallas' => 'S, M, L, XL',
            'peso_estimado' => 380,
        ]);

        $diseno->prototipo()->create([
            'peso_real' => 372,
            'fecha_prototipo' => '2026-04-15',
        ]);

        $diseno->remallado()->create([
            'tipo_remallado' => 'Remallado plano',
            'operario' => 'Rosa Ticona',
            'fecha_remallado' => '2026-04-10',
        ]);

        $diseno->tejido()->create([
            'tipo_tejido' => 'Punto inglés',
            'galga' => 6.2,
            'tension' => 3.2,
            'peso_por_metro' => 185,
        ]);

        $diseno->acabado()->create([
            'tipo_acabado' => 'Semimate',
            'temperatura_vapor' => 130,
            'minutos_proceso' => 45,
            'con_vapor' => true,
        ]);

        $diseno->ficha()->create([
            'es_muestra' => true,
            'modelo' => 'COLIBRI - CHOMPA SOLANA',
            'codigo_modelo' => '1366',
            'tipo_tejido' => 'Punto jacquard',
            'tipo_maquina' => 'gruesa',
            'tension' => 3.2,
            'galga' => 6.2,
            'punto' => 'tarjeta',
            'nombre_maquina' => 'Shima Seiki',
            'composicion' => '100% Baby Alpaca',
            'titulo_hilado' => '2/28',
            'color_principal' => 'VERDE',
            'observaciones' => 'Tener mucho cuidado en el vaporizado, no estirar demasiado LA PRETINA para que dé a la medida del ANCHO BASE SOBRE LA PRETINA.',
            'fecha_impresion' => '2026-06-17',
            'actualizado_tecnica_en' => '2026-02-11',
            'modificado_por' => 'Maky, Nelly',
        ]);

        $diseno->coloresIdea()->createMany([
            ['color' => 'Crema', 'tipo_tejido' => 'Punto inglés', 'composicion' => '100% Baby Alpaca', 'orden' => 0],
            ['color' => 'Naranja', 'tipo_tejido' => 'Punto jacquard', 'composicion' => '100% Baby Alpaca', 'orden' => 1],
        ]);

        $diseno->coloresMaterial()->createMany([
            ['color' => 'NARANJA', 'hex' => '#E8651A', 'codigo' => 'AM9110', 'empresa' => 'MICHELL', 'cantidad_pedida' => 8, 'orden' => 0],
            ['color' => 'TURQUEZA', 'hex' => '#2BA5A5', 'codigo' => '8422', 'empresa' => 'INCA TOP', 'cantidad_pedida' => 7, 'orden' => 1],
            ['color' => 'VERDE', 'hex' => '#5B8C4A', 'codigo' => 'VR1240', 'empresa' => 'MICHELL', 'cantidad_pedida' => 6, 'orden' => 2],
            ['color' => 'CELESTE', 'hex' => '#8CC4E6', 'codigo' => 'C986', 'empresa' => 'INCA TOP', 'cantidad_pedida' => 5, 'orden' => 3],
            ['color' => 'AZUL CIELO', 'hex' => '#3F8FDD', 'codigo' => 'CQ96', 'empresa' => 'INCA TOP', 'cantidad_pedida' => 4, 'orden' => 4],
            ['color' => 'TURQUEZ OCURO', 'hex' => '#1B5E6E', 'codigo' => 'M716', 'empresa' => 'MICHELL', 'cantidad_pedida' => 3, 'orden' => 5],
            ['color' => 'ARENA', 'hex' => '#C9A56F', 'codigo' => '284', 'empresa' => 'MICHELL', 'cantidad_pedida' => 3, 'orden' => 6],
        ]);

        $diseno->medidasPrototipo()->createMany([
            ['nombre' => 'Alto TOTAL', 's' => 58, 'm' => 60, 'l' => 62, 'xl' => 64, 'orden' => 0],
            ['nombre' => 'ANCHO Pecho', 's' => 48, 'm' => 51, 'l' => 54, 'xl' => 57, 'orden' => 1],
            ['nombre' => 'Largo de manga', 's' => 61, 'm' => 62, 'l' => 63, 'xl' => 64, 'orden' => 2],
            ['nombre' => 'ANCHO CUELLO', 's' => 21, 'm' => 21, 'l' => 22, 'xl' => 22, 'orden' => 3],
            ['nombre' => 'Ancho hombro', 's' => 9, 'm' => 10, 'l' => 10, 'xl' => 11, 'orden' => 4],
        ]);

        $diseno->medidasFicha()->createMany([
            ['nombre' => 'Alto TOTAL', 's' => 58, 'm' => 60, 'l' => 62, 'xl' => 64, 'orden' => 0],
            ['nombre' => 'ANCHO Pecho', 's' => 48, 'm' => 51, 'l' => 54, 'xl' => 57, 'orden' => 1],
            ['nombre' => 'Ancho base SOBRE LA PRETINA', 's' => 44, 'm' => 47, 'l' => 50, 'xl' => 53, 'orden' => 2],
            ['nombre' => 'SISA ADELANTE', 's' => 24, 'm' => 25, 'l' => 26, 'xl' => 27, 'orden' => 3],
            ['nombre' => 'Sisa ATRÁS', 's' => 26, 'm' => 27, 'l' => 28, 'xl' => 29, 'orden' => 4],
            ['nombre' => 'MAnga sola EXTERIOR (RANGLA)', 's' => 65, 'm' => 66, 'l' => 67, 'xl' => 68, 'orden' => 5],
            ['nombre' => 'Manga sola (desde sisa inferior)', 's' => 42, 'm' => 43, 'l' => 44, 'xl' => 45, 'orden' => 6],
            ['nombre' => 'ANCHO CUELLO', 's' => 21, 'm' => 21, 'l' => 22, 'xl' => 22, 'orden' => 7],
            ['nombre' => 'Bajada de cuello', 's' => 7.5, 'm' => 7.5, 'l' => 8.5, 'xl' => 8.5, 'orden' => 8],
            ['nombre' => 'Ancho puño', 's' => 8, 'm' => 9, 'l' => 10, 'xl' => 11, 'orden' => 9],
            ['nombre' => 'Ancho brazo', 's' => 17, 'm' => 17.5, 'l' => 18, 'xl' => 18.5, 'orden' => 10],
            ['nombre' => 'Ancho brazo ENCIMA DE LA PRETINA', 's' => 17, 'm' => 17.5, 'l' => 18, 'xl' => 18.5, 'orden' => 11],
            ['nombre' => 'Ancho hombro', 's' => 9, 'm' => 10, 'l' => 10, 'xl' => 11, 'orden' => 12],
            ['nombre' => 'ALTO PRETINAS MANGAS CUERPO', 's' => 7, 'm' => 7, 'l' => 7, 'xl' => 7, 'orden' => 13],
            ['nombre' => 'ALTO PRETINA CUELLO', 's' => 4, 'm' => 4, 'l' => 4, 'xl' => 4, 'orden' => 14],
        ]);

        $diseno->insumos()->createMany([
            ['nombre' => 'Botones', 'cantidad' => 4, 'codigo' => 'BOT-001', 'orden' => 0],
            ['nombre' => 'Cierre', 'cantidad' => null, 'codigo' => null, 'orden' => 1],
        ]);

        $diseno->notas()->createMany([
            ['seccion' => 'diseno', 'contenido' => 'Revisar tensión en costuras laterales.'],
            ['seccion' => 'general', 'contenido' => 'Muestra con buena caída.'],
            ['seccion' => 'correcciones', 'contenido' => 'Ajustar largo 2 cm.'],
            ['seccion' => 'remallado', 'contenido' => 'Costuras prolijas, sin exceso de hilo.'],
            ['seccion' => 'tejido', 'contenido' => 'Tensión uniforme en todo el tejido.'],
            ['seccion' => 'acabado', 'contenido' => 'No estirar en el vaporizado.'],
        ]);

        $diseno->observaciones()->createMany([
            [
                'usuario_id' => $fabrica?->id,
                'autor' => 'Carlos Flores',
                'rol' => Usuario::ROL_FABRICA,
                'observado_en' => '2026-05-08 16:45:00',
                'comentario' => 'Corrección de costuras laterales completada. Muestra lista para revisión.',
            ],
            [
                'usuario_id' => $eu?->id,
                'autor' => 'Ana Gómez (EU)',
                'rol' => Usuario::ROL_EU,
                'observado_en' => '2026-04-20 11:00:00',
                'comentario' => 'El color naranja no coincide con la paleta aprobada. Solicitar muestra física.',
            ],
            [
                'usuario_id' => null,
                'autor' => 'Lucía Mamani',
                'rol' => Usuario::ROL_FABRICA,
                'observado_en' => '2026-04-02 14:10:00',
                'comentario' => 'Revisada la tensión del hilo. Se recomienda usar galga 6.2 para uniformidad.',
            ],
            [
                'usuario_id' => null,
                'autor' => 'María Quispe',
                'rol' => Usuario::ROL_FABRICA,
                'observado_en' => '2026-03-15 09:30:00',
                'comentario' => 'Se ajustó el largo del cuerpo en 2 cm según indicación del cliente.',
            ],
        ]);

        $diseno->revisiones()->createMany([
            [
                'usuario_id' => $eu?->id,
                'revisor' => 'Ana Gómez',
                'estado' => RevisionDiseno::OBSERVADA,
                'revisado_en' => '2026-04-20 11:05:00',
                'comentario' => 'El color naranja no coincide con la paleta aprobada. Solicitar muestra física antes de continuar.',
            ],
            [
                'usuario_id' => null,
                'revisor' => 'Roberto Salas',
                'estado' => RevisionDiseno::APROBADA,
                'revisado_en' => '2026-05-12 09:20:00',
                'comentario' => 'La caída del tejido es correcta. Las proporciones del cuello están bien. Apruebo con observación de revisar costuras.',
            ],
        ]);

        Diseno::create([
            'codigo' => 'AK-D-002',
            'nombre' => 'Cardigan Merino',
            'version' => 1,
            'estado_aprobacion' => 'en_desarrollo',
        ]);

        Diseno::create([
            'codigo' => 'AK-D-003',
            'nombre' => 'Chaleco Reversible',
            'version' => 1,
            'estado_aprobacion' => 'en_desarrollo',
        ]);
    }
}
