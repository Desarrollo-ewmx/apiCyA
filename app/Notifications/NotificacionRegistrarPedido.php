<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
// Models
use App\Models\Sistema;
// Otros
use Carbon\Carbon;

class NotificacionRegistrarPedido extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $year = Carbon::parse(Sistema::datos()->sistemaFindOrFail()->year_de_ini);
        return (new MailMessage)
        ->subject($this->plantilla->asunt)
        ->view(
            'correo.' . $this->plantilla->id, [

                // SISTEMA
                'nombre_de_la_empresa'              => Sistema::datos()->sistemaFindOrFail()->emp, 
                'nombre_de_la_empresa_abreviado'    => Sistema::datos()->sistemaFindOrFail()->emp_abrev,
                'telefono_fijo'                     => Sistema::datos()->sistemaFindOrFail()->lad_fij.Sistema::datos()->sistemaFindOrFail()->tel_fij,
                'extension'                         => Sistema::datos()->sistemaFindOrFail()->ext, 
                'telefono_movil'                    => Sistema::datos()->sistemaFindOrFail()->lad_mov.Sistema::datos()->sistemaFindOrFail()->tel_mov, 
                'direccion_uno'                     => Sistema::datos()->sistemaFindOrFail()->direc_uno,
                'correo_ventas'                     => Sistema::datos()->sistemaFindOrFail()->corr_vent,
                'year_de_inicio_de_la_empresa'      => $year->year, 
                'pagina_web_de_la_empresa'          => Sistema::datos()->sistemaFindOrFail()->pag,
                'pagina_de_inicio_del_sistema'      => url(config('app.url')),
                'year_actual'                       => date("Y"),
                
                // USUARIO
                'nombre_completo_del_usuario'       => $notifiable->nom . ' ' . $notifiable->apell,
                'nombre_del_usuario'                => $notifiable->nom,
                'apellido_del_usuario'              => $notifiable->apell,
                'email_registro_del_usuario'        => $notifiable->email_registro,

                // EXTRAS
                'numero_de_pedido'                  => $this->pedido->num_pedido,
            ]
        );
        // return (new MailMessage)
        //             ->line('The introduction to the notification.')
        //             ->action('Notification Action', url('/'))
        //             ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
